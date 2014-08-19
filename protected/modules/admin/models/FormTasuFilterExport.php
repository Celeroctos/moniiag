<?php

class FormTasuFilterExport extends CFormModel
{
    public $doctorId;
	public $greetingDate;

    public function rules()
    {
        return array(
            array(
                'doctorId, greetingDate', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'doctorId' => 'Врач',
			'greetingDate' => 'Дата приёма'
        );
    }
}


?>