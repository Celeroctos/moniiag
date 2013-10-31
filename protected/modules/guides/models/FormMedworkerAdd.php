<?php

class FormMedworkerAdd extends CFormModel
{
    public $name;
    public $type;
    public $id;

    public function rules()
    {
        return array(
            array(
                'name, type', 'required'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'name'=> 'Наименование',
            'type' => 'Тип персонала',
        );
    }
}


?>