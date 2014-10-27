<?php
class UserIdentity extends CUserIdentity {
    private $_id;
	public function authenticateStep1() {
        $record = User::model()->findByAttributes(array('login' => $this->username));
        if($record === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else
            if($record->password !== md5(md5($this->password))) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            $this->errorCode = self::ERROR_NONE;
        }
				
		$roles = RoleToUser::model()->findAllRolesByUser($record->id);
		$currentPriority = -1;
		foreach($roles as $role) {
			if($currentPriority < $role['priority']) {
				$currentPriority = $role['priority'];
				$url = $role['url'];
			}
		}

		$this->setState('id', $record->id);
		$this->setState('roleId', $roles);
		$this->setState('login', $record->login);
		$this->setState('password', $this->password);
		// Проверяем, сколько сотрудников прикреплено к пользователю. Если больше одного - выводить окно для выбора сотрудника в методе на уровень выше	
		$numEmployeesToUser = count(Doctor::model()->findAll('user_id = :user_id', array(':user_id' => $record->id)));
		if($numEmployeesToUser == 1) { // Если всего один, то сразу вынимать все данные
			$this->authenticateStep2($record);
		}
        
		return !$this->errorCode;

	}
	
	public function authenticateStep2($record = false, $employeeForm = false) {
		if($record === false) {
			$record = User::model()->findByAttributes(array('login' => $this->username));
		}

		$this->_id = $record->id;
		$employee = Doctor::model()->findByPk($employeeForm->id);
		if($employee != null) {
			$ward = Ward::model()->findByPk($employee->ward_code);
		} else {
			$ward = null;
		}
		$url = '';

		// Данные юзера
		$this->setState('username', $record->username);
		$this->setState('doctorId', $employee->id);
		$this->setState('medworkerId', $employee->post_id);
		$this->setState('enterpriseId', $ward != null ? $ward->enterprise_id : null);
		$this->setState('fio', $employee->last_name.' '.$employee->first_name.' '.$employee->middle_name);
		$this->setState('startpageUrl', $url);
		$this->setState('medcardGenRuleId', $ward != null ? $ward->rule_id : null); 
		if(isset($_SESSION['fontSize'])) {
			$this->setState('fontSize', $_SESSION['fontSize']);
		} else {
			$this->setState('fontSize', 12);
		}
		return true;
	}

    public function wrongLogin()
    {
        return ($this->errorCode == self::ERROR_USERNAME_INVALID);
    }

    public function wrongPassword()
    {
        return ($this->errorCode == self::ERROR_PASSWORD_INVALID);
    }
}

?>