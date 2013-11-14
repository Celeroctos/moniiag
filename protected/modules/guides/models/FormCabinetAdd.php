<?php

class FormCabinetAdd extends FormMisDefault
{
    public $wardId;
    public $id;
    public $enterpriseId;
    public $cabNumber;
    public $description;

    public function rules()
    {
        return array(
            array(
                'wardId, enterpriseId, cabNumber', 'required'
            ),
            array(
                'description,  id', 'safe'
            )
        );
    }


    public function attributeLabels()
    {
        return array(
            'id'=> 'Код',
            'wardId' => 'Отделение',
            'enterpriseId' => 'Учреждение',
            'cabNumber' => 'Номер',
            'description' => 'Описание',
        );
    }
}


?>