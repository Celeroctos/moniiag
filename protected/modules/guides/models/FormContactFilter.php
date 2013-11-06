<?php

class FormContactFilter extends CFormModel
{
    public $wardCode;
    public $enterpriseCode;
    public $employeeCode;


    public function rules()
    {
        return array(
            array(
                'wardCode, enterpriseCode, employeeCode', 'numerical'
            )
        );
    }


    public function attributeLabels()
    {
        return array(
            'enterpriseCode' => 'Учреждение',
            'wardCode' => 'Отделение',
            'employeeCode' => 'Сотрудник'
        );
    }
}


?>