<?php
class FormDirectionForPatientAdd extends FormMisDefault
{
    public $id;
    public $type;
    public $isPregnant;
    public $ward;

    public function rules()
    {
        return array(
            array(
                'type, isPregnant, ward', 'required'
            ),
            array(
                'id', 'safe'
            ),
            array(
                'id', 'numerical'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'type' => 'Тип госпитализации',
            'isPregnant' => 'Беременная',
			'ward' => 'Отделение'
        );
    }
}


?>