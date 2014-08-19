<?php

class FormGreetingsFilter extends FormMisDefault
{
    public $greetingDateFrom;
    public $greetingDateTo;
    public $wardId;
	public $doctorId;
	public $medpersonalId;

    public function rules()
    {
        return array(
            array(
                'greetingDateFrom, greetingDateTo, wardId, doctorId, medpersonalId', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'greetingDateFrom' => 'С',
            'greetingDateTo' => 'По',
			'wardId' => 'Отделение',
			'doctorId' => 'Врач',
			'medpersonalId' => 'Специализация'
        );
    }
}


?>