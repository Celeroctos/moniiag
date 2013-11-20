<?php

class FormValueAdd extends CFormModel
{
    public $value;
    public $id;

    public function rules()
    {
        return array(
            array(
                'value', 'required'
            ),
            array(
                'id', 'safe'
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