<?php

class FormTasuBufferAdd extends CFormModel
{
    public $greetingId;
    public $id;

    public function rules()
    {
        return array(
            array(
                'greetingId', 'required'
            ),
            array(
                'id', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'greetingId' => 'Выберите приём',
        );
    }
}


?>