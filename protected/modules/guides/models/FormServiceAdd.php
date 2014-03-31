<?php

class FormServiceAdd extends CFormModel
{
    public $name;
    public $code;
    public $id;

    public function rules()
    {
        return array(
            array(
                'name, code', 'required'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Описание услуги',
            'code' => 'Код в ТАСУ',
        );
    }
}


?>