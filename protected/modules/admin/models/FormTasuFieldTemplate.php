<?php

class FormTasuFieldTemplate extends CFormModel
{
    public $name;
    public $template;
    public $table;

    public function rules()
    {
        return array(
            array(
                'name, template, table', 'required'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Название шаблона',
        );
    }
}


?>