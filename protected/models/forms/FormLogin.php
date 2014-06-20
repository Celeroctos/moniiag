<?php

class FormLogin extends FormMisDefault
{
    public $login;
    public $password;

    public function rules()
    {
        return array(
            array(
                'password, login', 'required'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'login' => '',
            'password' => '',
        );
    }
}


?>