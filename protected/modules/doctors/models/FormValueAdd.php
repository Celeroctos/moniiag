<?php

class FormValueAdd extends CFormModel
{
    public $value;
    public $id;
    public $guideId;

    public function rules()
    {
        return array(
            array(
                'value', 'required'
            ),
            array(
                'id, guideId', 'safe'
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