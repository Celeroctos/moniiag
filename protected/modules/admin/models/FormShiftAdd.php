<?php

class FormShiftAdd extends CFormModel
{
    public $id;
	public $timeBegin;
    public $timeEnd;

    public function rules()
    {
        return array(
            array(
                'timeBegin, timeEnd', 'required'
            ),
            array(
                'timeBegin, timeEnd', 'date', 'format' => 'H:m'
            ),
			array(
                'id', 'safe'
			),
        );
    }

    public function attributeLabels()
    {
        return array(
            'timeBegin' => 'Время начала приёма (чч:мм)',
			'timeEnd' => 'Время конца приёма (чч:мм)'
        );
    }
}


?>