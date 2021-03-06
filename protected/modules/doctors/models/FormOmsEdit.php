<?php

class FormOmsEdit extends FormMisDefault
{
    public $policy;
    public $omsSeries;
    public $lastName;
    public $firstName;
    public $middleName;
    public $gender;
    public $birthday;
    public $id;
    public $omsType;
    public $policyGivedate;
    public $policyEnddate;
    public $status;
    public $insurance;
    public $region;

    public function rules()
    {
        return array(
            array(
                'policy, lastName, firstName, gender, birthday, omsType, policyGivedate, status', 'required'
            ),
            array(
                'id', 'numerical'
            ),
            array(
                'middleName, policyEnddate', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'policy' => 'Номер полиса ОМС',
            'lastName' => 'Фамилия',
            'firstName' => 'Имя',
            'middleName' => 'Отчество',
            'gender' => 'Пол',
            'birthday' => 'Дата рождения',
            'omsType' => 'Тип ОМС',
            'policyGivedate' => 'Дата выдачи',
            'policyEnddate' => 'Дата погашения полиса',
            'status' => 'Статус'
        );
    }
}


?>