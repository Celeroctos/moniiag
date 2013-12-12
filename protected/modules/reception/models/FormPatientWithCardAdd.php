<?php

class FormPatientWithCardAdd extends FormMisDefault
{
    public $doctype;
    public $serie;
    public $docnumber;
    public $whoGived;
    public $documentGivedate;
    public $addressReg;
    public $address;
    public $workPlace;
    public $workAddress;
    public $post;
    public $contact;
    public $snils;
    public $invalidGroup;
    public $policy;
    public $cardNumber;

    public function rules()
    {
        return array(
            array(
                'doctype, serie, docnumber, documentGivedate, addressReg, address, contact, whoGived', 'required'
            ),
            array(
                'workPlace, workAddress, post, snils, invalidGroup, policy, cardNumber', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'doctype' => 'Тип документа',
            'serie' => 'Серия',
            'docnumber' => 'Номер',
            'whoGived' => 'Кем выдан',
            'documentGivedate' => 'Дата выдачи',
            'addressReg' => 'Адрес регистрации',
            'address' => 'Адрес проживания',
            'workPlace' => 'Место работы',
            'workAddress' => 'Адрес работы',
            'post' => 'Должность',
            'contact' => 'Контактные данные',
            'snils' => 'СНИЛС',
            'invalidGroup' => 'Группа инвалидности'
        );
    }
}


?>