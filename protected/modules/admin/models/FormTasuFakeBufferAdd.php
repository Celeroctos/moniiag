<?php

class FormTasuFakeBufferAdd extends CFormModel
{
    public $id;
    public $cardNumber;
    public $doctorId;
    public $primaryDiagnosis;
    public $greetingDate;

    public function rules()
    {
        return array(
            array(
                'cardNumber, doctorId, primaryDiagnosis, greetingDate', 'required'
            ),
            array(
                'id', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'cardNumber' => 'Номер карты',
            'doctorId' => 'Врач',
            'primaryDiagnosis' => 'Первичный диагноз',
            'greetingDate' => 'Дата приёма'
        );
    }
}


?>