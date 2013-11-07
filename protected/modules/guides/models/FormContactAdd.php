<?php

class FormContactAdd extends CFormModel
{
    public $contactValue;
    public $type;
    public $employeeId;
    public $id;

    public function rules()
    {
        return array(
            array(
                'type', 'required'
            ),
            array(
                'contactValue, employeeId', 'safe'
            ),
            array(
                'type', 'numerical'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'contactValue'=> 'Значение контакта',
            'type' => 'Тип',
            'employeeId' => 'Сотрудник'
        );
    }
}


?>