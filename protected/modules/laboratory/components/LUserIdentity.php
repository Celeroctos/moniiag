<?php

class LUserIdentity extends CUserIdentity {

	/**
	 * Get some state from opened user's session
	 * @param string $key - Name of value to get
	 * @return mixed - Value associated with key
	 */
	public static function get($key) {
		return Yii::app()->user->getState($key);
	}

    public function authenticate() {

        $record = User::model()->findByAttributes(array(
            'password' => md5(md5($this->password)),
            'login' => $this->username
        ));

        if($record === null) {

            $this->errorCode = self::ERROR_USERNAME_INVALID;
            $this->errorCode = self::ERROR_PASSWORD_INVALID;

            return false;
        }

        $this->_id = $record->id;

        $employee = Doctor::model()->findByPk($record->employee_id);
        $roles = RoleToUser::model()->findAllRolesByUser($record->id);

        if($employee != null) {
            $ward = Ward::model()->findByPk($employee->ward_code);
        } else {
            $ward = null;
        }

        $currentPriority = -1;
        $url = '';

        foreach($roles as $role) {
            if($currentPriority < $role['priority']) {
                $currentPriority = $role['priority'];
                $url = $role['url'];
            }
        }

        // Данные юзера
        $this->setState('login', $record->login);
        $this->setState('id', $record->id);
        $this->setState('username', $record->username);
        $this->setState('roleId', $roles);
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

        $this->errorCode = self::ERROR_NONE;

        return !$this->errorCode;
    }

    public function wrongLogin() {
        return ($this->errorCode == self::ERROR_USERNAME_INVALID);
    }

    public function wrongPassword() {
        return ($this->errorCode == self::ERROR_PASSWORD_INVALID);
    }

    private $_id;
} 