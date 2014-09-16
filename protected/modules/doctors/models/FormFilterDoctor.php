<?php

class FormFilterDoctor extends CFormModel
{
    public $doctorId;

    public function rules()
    {
        return array(
            array(
                'doctorId', 'numerical'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'doctorId' => 'Врач',
        );
    }
}


?>