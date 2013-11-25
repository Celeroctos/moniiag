<?php

class FormPregnantSearch extends FormMisDefault
{
    public $lastName;
    public $firstName;
    public $middleName;
    public $omsNumber;
    public $cardNumber;
    public $id;

    public function rules()
    {
        return array(
            array(
                'lastName, firstName, middleName, omsNumber, cardNumber, id', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'omsNumber' => 'ОМС',
            'lastName' => 'Фамилия',
            'firstName' => 'Имя',
            'middleName' => 'Отчество',
            'cardNumber' => 'Номер карты'
        );
    }
}


?>