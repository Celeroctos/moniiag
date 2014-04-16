<?php

class FormClinicalDiagnosisAdd extends CFormModel
{
    public $description;
    public $id;

    public function rules()
    {
        return array(
            array(
                'description', 'required'
            ),
            array(
                'id', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
			'description' => 'Название',
        );
    }
}


?>