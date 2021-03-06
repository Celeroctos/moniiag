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
    public $maxInWaitingLine;
    public $maxGreetingsInCallcenter;
    public $defaultOmsType;
    public $waitingLineDateWriting;
    public $waitingLineTimeWriting;

    public function rules()
    {
        return array(
            array(
                'timePerPatient, firstVisit, quote, shiftType, pregnantGreetingsLimit, primaryGreetingsLimit, maxInWaitingLine, maxGreetingsInCallcenter,defaultOmsType,waitingLineDateWriting,waitingLineTimeWriting', 'required'
            ),
			array(
                'timePerPatient, firstVisit, quote, shiftType, calendarType, maxInWaitingLine, maxGreetingsInCallcenter', 'numerical'
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
            'primaryGreetingsLimit' => 'Крайнее время записи на первичный приём',
            'maxInWaitingLine' => 'Количество пациентов по живой очереди',
            'maxGreetingsInCallcenter' => 'Количество пациентов (в день) через Call-центр на врача',
            'defaultOmsType' => 'Тип полиса по умолчанию',
            'waitingLineDateWriting' => 'Запись в живую очередь на будущие даты',
            'waitingLineTimeWriting' => 'Запись в живую очередь после окончания приёма'
        );
    }
}


?>