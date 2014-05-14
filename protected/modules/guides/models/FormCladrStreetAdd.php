<?php

class FormCladrStreetAdd extends CFormModel
{
    public $name;
    public $codeCladr;
    public $codeRegion;
    public $codeDistrict;
    public $codeSettlement;
    public $id;

    public function rules()
    {
        return array(
            array(
                'name, codeCladr, codeRegion, codeDistrict, codeSettlement', 'required'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Название улицы',
            'codeCladr' => 'Код в КЛАДР',
            'codeRegion' => 'Регион',
            'codeDistrict' => 'Район',
            'codeSettlement' => 'Населённый пункт'
        );
    }
}


?>