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
                'name, codeCladr', 'required'
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