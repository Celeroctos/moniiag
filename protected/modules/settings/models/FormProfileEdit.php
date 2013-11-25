<?php

class FormProfileEdit extends FormMisDefault
{
    public $id;
    public $login;
    public $username;
    public $password;
    public $passwordRepeat;

    public function rules()
    {
        return array(
            array(
                'login, username', 'required'
            ),
            array(
                'id, password, passwordRepeat', 'safe'
            ),
        );
    }

    public function attributeLabels()
    {
        return array(
            'login' => 'Логин',
            'username' => 'Отображаемое имя',
            'password' => 'Пароль (если не хотите менять пароль, оставьте это поле пустым)',
            'passwordRepeat' => 'Повтор пароля (если не хотите менять пароль, оставьте это поле пустым)'
        );
    }
}


?>