<?php

class FormValueAdd extends CFormModel
{
    public $value;
    public $id;
    public $controlId;
    public $greetingId;

    public function rules()
    {
        return array(
            array(
                'value', 'required'
            ),
            array(
                'id, controlId, greetingId', 'safe'
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