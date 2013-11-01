<?php

class FormContactAdd extends CFormModel
{
    public $contactValue;
    public $type;
    public $id;

    public function rules()
    {
        return array(
            array(
                'type', 'required'
            ),
            array(
                'contactValue', 'safe'
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
            'type' => 'Тип'
        );
    }
}


?>