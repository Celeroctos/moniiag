<?php

class FormEmployeeFilter extends CFormModel
{
    public $wardCode;
    public $enterpriseCode;


    public function rules()
    {
        return array(
            array(
                'wardCode, enterpriseCode', 'numerical'
            )
        );
    }


    public function attributeLabels()
    {
        return array(
            'enterpriseCode' => 'Учреждение',
            'wardCode' => 'Отделение'
        );
    }
}


?>