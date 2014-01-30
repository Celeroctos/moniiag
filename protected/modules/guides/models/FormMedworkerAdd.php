<?php

class FormMedworkerAdd extends CFormModel
{
    public $name;
    public $type;
    public $isForPregnants;
    public $id;
    public $paymentType;

    public function rules()
    {
        return array(
            array(
                'name, type, isForPregnants, paymentType', 'required'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'name'=> 'Наименование',
            'type' => 'Тип персонала',
            'isForPregnants' => 'Может наблюдать за беременными',
            'paymentType' => 'Тип оплаты'
        );
    }
}


?>