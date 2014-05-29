<?php

class FormSheduleSettings extends CFormModel
{
    public $timePerPatient;
	public $firstVisit;
    public $quote;
    public $shiftType;
    public $calendarType;
    public $primaryGreetingsLimit;
    public $pregnantGreetingsLimit;

    public function rules()
    {
        return array(
            array(
                'timePerPatient, firstVisit, quote, shiftType, pregnantGreetingsLimit, primaryGreetingsLimit', 'required'
            ),
			array(
                'timePerPatient, firstVisit, quote, shiftType, calendarType', 'numerical'
			),
        );
    }

    public function attributeLabels()
    {
        return array(
            'timePerPatient' => 'Норма времени на одного пациента (минут)',
			'firstVisit' => 'Количество первичных осмотров  за смену',
            'quote' => 'Квота на запись на будущие числа',
            'shiftType' => 'Правило организации смен работы',
            'calendarType' => 'Тип календаря при записи пациента в регистратуре',
            'pregnantGreetingsLimit' => 'Крайнее время записи для беременных',
            'primaryGreetingsLimit' => 'Крайнее время записи на первичный приём'
        );
    }
}


?>