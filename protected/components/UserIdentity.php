<?php

class UserIdentity extends CUserIdentity
{
    private $_id;

	public function authenticate()
	{
        $record = User::model()->findByAttributes(array('login' => $this->username));
        if($record === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else if($record->password !== crypt($this->password, $record->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            $this->_id = $record->id;
            $employee = Doctor::model()->findByPk($record->employee_id);
            $roles = RoleToUser::model()->findAllRolesByUser($record->id);
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
            $this->SetState('doctorId', $employee->id);
            $this->setState('medworkerId', $employee->post_id);
            $this->setState('fio', $employee->last_name.' '.$employee->first_name.' '.$employee->middle_name);
            $this->setState('startpageUrl', $url);
            if(isset($_SESSION['fontSize'])) {
                $this->setState('fontSize', $_SESSION['fontSize']);
            } else {
                $this->setState('fontSize', 12);
            }

            $this->errorCode = self::ERROR_NONE;
        }

        return !$this->errorCode;

	}
}

?>