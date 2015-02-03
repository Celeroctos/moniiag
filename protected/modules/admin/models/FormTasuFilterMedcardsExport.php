<?php

class FormTasuFilterMedcardsExport extends CFormModel
{
	public $dateFrom;
    public $dateTo;

    public function rules()
    {
        return array(
            array(
                'dateFrom, dateTo', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
			'dateFrom' => 'С',
            'dateTo' => 'По'
        );
    }
}


?>