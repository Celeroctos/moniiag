<?php

class FormValueAdd extends CFormModel
{
    public $value;
    public $id;
    public $controlId;
    public $greetingId;
    public $chooserId;

    public function rules()
    {
        return array(
            array(
                'value', 'required'
            ),
            array(
                'id, controlId, greetingId, chooserId', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'value' => 'Значение',
        );
    }
}


?>