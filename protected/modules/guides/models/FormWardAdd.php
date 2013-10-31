<?php

class FormWardAdd extends CFormModel
{
    public $name;
    public $enterprise;
    public $id;

    public function rules()
    {
        return array(
            array(
                'name, enterprise', 'required'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'name'=> 'Название отделения',
            'enterprise' => 'Учреждение',
        );
    }
}


?>