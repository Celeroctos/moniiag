<?php

class FormPrivilegeAdd extends CFormModel
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
            'name' => 'Описание льготы',
            'code' => 'Код льготы',
        );
    }
}


?>