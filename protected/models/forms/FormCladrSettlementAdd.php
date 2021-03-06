<?php

class FormCladrSettlementAdd extends CFormModel
{
    public $name;
    public $codeCladr;
    public $codeRegion;
    public $codeDistrict;
    public $id;

    public function rules()
    {
        return array(
            array(
                //'name, codeCladr, codeRegion, codeDistrict', 'required'
                  'name, codeRegion', 'required'
            ),
            array(
                //'name, codeCladr, codeRegion, codeDistrict', 'required'
                'codeCladr, codeDistrict', 'safe'
            ),
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Название населенного пункта',
            'codeCladr' => 'Код в КЛАДР',
            'codeRegion' => 'Регион',
            'codeDistrict' => 'Район'
        );
    }
}


?>