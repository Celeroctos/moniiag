<?php

class FormOmsEdit extends FormMisDefault
{
    public $policy;
    public $lastName;
    public $firstName;
    public $middleName;
    public $gender;
    public $birthday;
    public $id;

    public function rules()
    {
        return array(
            array(
                'policy, lastName, firstName, gender, birthday', 'required'
            ),
            array(
                'id', 'numerical'
            ),
            array(
                'middleName', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'policy' => 'Номер ОМС',
            'lastName' => 'Фамилия',
            'firstName' => 'Имя',
            'middleName' => 'Отчество',
            'gender' => 'Пол',
            'birthday' => 'Дата рождения'
        );
    }
}


?>