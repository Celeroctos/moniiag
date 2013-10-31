<?php

class FormEnterpriseAdd extends CFormModel
{
    public $shortName;
    public $fullName;
    public $addressFact;
    public $addressJur;
    public $phone;
    public $bank;
    public $bankAccount;
    public $inn;
    public $kpp;
    public $type;
    public $id;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // name, email, subject and body are required
            array(
                'shortName, fullName, addressFact, addressJur, phone, bank, bankAccount, inn, kpp, type', 'required'
            )
        );
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'shortName'=> 'Краткое название',
            'fullName' => 'Полное название',
            'addressFact' => 'Адрес фактический',
            'addressJur' => 'Адрес юридический',
            'phone' => 'Телефон',
            'bank' => 'Банк',
            'bankAccount' => 'Расчётный счёт',
            'inn' => 'ИНН',
            'kpp' => 'КПП',
            'type' => 'Тип'
        );
    }
}


?>