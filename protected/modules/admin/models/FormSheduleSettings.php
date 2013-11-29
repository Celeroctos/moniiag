<?php

class FormSheduleSettings extends CFormModel
{
    public $timePerPatient;
	public $firstVisit;
    public $quote;
    public $shiftType;

    public function rules()
    {
        return array(
            array(
                'timePerPatient, firstVisit, quote, shiftType', 'required'
            ),
			array(
                'timePerPatient, firstVisit, quote, shiftType', 'numerical'
			),
        );
    }

    public function attributeLabels()
    {
        return array(
            'timePerPatient' => 'Норма времени на одного пациента (минут)',
			'firstVisit' => 'Количество первичных осмотров  за смену',
            'quote' => 'Квота на запись на будущие числа',
            'shiftType' => 'Правило организации смен работы'
        );
    }
}


?>