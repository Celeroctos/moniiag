<?php

class FormPregnantAdd extends FormMisDefault
{
    public $doctorId;
    public $registerType;
    public $cardId;
    public $id;

    public function rules()
    {
        return array(
            array(
                'doctorId, registerType', 'required'
            ),
            array(
                'id, cardId', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'doctorId' => 'Наблюдающий врач',
            'registerType' => 'Тип учёта'
        );
    }
}


?>