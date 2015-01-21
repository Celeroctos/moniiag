<?php

class FormSensorAddEdit extends FormMisDefault
{
    public $lastName;
    public $firstName;
    public $middleName;
	public $room;
	public $bed;

    public function rules()
    {
        return array(
            array(
                'lastName, firstName, room, bed', 'required'
            ),
            array(
                'middleName', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'lastName' => 'Фамилия',
            'firstName' => 'Имя',
			'middleName' => 'Отчество',
			'room' => 'Палата',
			'bed' => 'Койка'
        );
    }
}


?>