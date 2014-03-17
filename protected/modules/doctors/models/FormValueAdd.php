<?php

class FormValueAdd extends CFormModel
{
    public $value;
    public $id;
    public $controlId;

    public function rules()
    {
        return array(
            array(
                'value', 'required'
            ),
            array(
                'id, controlId', 'safe'
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