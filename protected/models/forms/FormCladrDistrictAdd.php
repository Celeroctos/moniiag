<?php

class FormCladrDistrictAdd extends CFormModel
{
    public $name;
    public $codeCladr;
    public $codeRegion;
    public $id;

    public function rules()
    {
        return array(
            array(
                //'name, codeCladr, codeRegion', 'required'
                 'name, codeRegion', 'required'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Название района',
            'codeCladr' => 'Код в КЛАДР',
            'codeRegion' => 'Регион',
        );
    }
}


?>