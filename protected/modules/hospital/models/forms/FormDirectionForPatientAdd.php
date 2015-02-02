<?php
class FormDirectionForPatientAdd extends FormMisDefault
{
    public $id;
    public $type;
    public $isPregnant;
    public $wardId;
    public $omsId;
    public $doctorId;
    public $pregnantTerm;

    public function rules()
    {
        return array(
            array(
                'type, isPregnant, wardId, omsId, doctorId, pregnantTerm', 'required'
            ),
            array(
                'id', 'safe'
            ),
            array(
                'id, omsId, doctorId, pregnantTerm', 'numerical'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'type' => 'Тип госпитализации',
            'isPregnant' => 'Беременная',
			'wardId' => 'Отделение',
            'pregnantTerm' => 'Срок беременности (недель)'
        );
    }
}


?>