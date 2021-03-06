<?php

class FormOmsEdit extends FormMisDefault
{
    public $policy;
    public $lastName;
    public $omsSeries;
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
                'middleName, policyEnddate, omsSeries', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'policy' => 'Номер полиса',
            'lastName' => 'Фамилия',
            'firstName' => 'Имя',
            'middleName' => 'Отчество',
            'gender' => 'Пол',
            'birthday' => 'Дата рождения',
            'omsType' => 'Тип полиса',
            'policyGivedate' => 'Дата выдачи',
            'policyEnddate' => 'Дата погашения полиса',
            'status' => 'Статус'
        );
    }
}


?>