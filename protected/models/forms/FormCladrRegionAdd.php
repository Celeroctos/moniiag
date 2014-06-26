<?php

class FormCladrRegionAdd extends CFormModel
{
    public $name;
    public $codeCladr;
    public $id;

    public function rules()
    {
        return array(
            array(
                //'name, codeCladr', 'required'
                 'name', 'required'
            ),
            array(
                'codeCladr', 'safe'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Название региона',
            'codeCladr' => 'Код в КЛАДР'
        );
    }
}


?>