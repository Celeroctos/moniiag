<?php
class FormDirectionForPatientAdd extends FormMisDefault
{
    public $id;
    public $type;
    public $isPregnant;
    public $wardId;
    public $omsId;
    public $doctorId;

    public function rules()
    {
        return array(
            array(
                'type, isPregnant, wardId, omsId, doctorId', 'required'
            ),
            array(
                'id', 'safe'
            ),
            array(
                'id, omsId, doctorId', 'numerical'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'type' => 'Тип госпитализации',
            'isPregnant' => 'Беременная',
			'wardId' => 'Отделение'
        );
    }
}


?>