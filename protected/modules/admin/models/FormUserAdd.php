<?php

class FormUserAdd extends CFormModel
{
    public $username;
    public $login;
    public $password;
    public $passwordRepeat;
    public $roleId;
    public $id;
    public $employeeId;


    public function rules()
    {
        return array(
            array(
                'username, login, roleId, employeeId', 'required'
            ),
            array(
                'id, password, passwordRepeat', 'safe'
            ),
            array(
                'password', 'length', 'min' => 6
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'username'=> 'Отображаемое имя',
            'login' => 'Логин',
            'password' => 'Пароль',
            'roleId' => 'Роль',
            'passwordRepeat' => 'Повтор пароля',
            'employeeId' => 'Ассоциированный сотрудник'
        );
    }
}

?>