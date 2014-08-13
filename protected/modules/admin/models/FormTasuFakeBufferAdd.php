<?php

class FormTasuFakeBufferAdd extends CFormModel
{
    public $id;
    public $cardNumber;
    public $doctorId;
    public $primaryDiagnosis;
	public $secondaryDiagnosis;
    public $greetingDate;
	public $wardId;

    public function rules()
    {
        return array(
            array(
                'cardNumber, doctorId, primaryDiagnosis, greetingDate', 'required'
            ),
            array(
                'id, secondaryDiagnosis, wardId', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'cardNumber' => 'Номер карты',
            'doctorId' => 'Врач',
            'primaryDiagnosis' => 'Первичный диагноз',
			'secondaryDiagnosis' => 'Вторичные диагнозы',
            'greetingDate' => 'Дата приёма',
			'wardId' => 'Отделение'
        );
    }
}


?>