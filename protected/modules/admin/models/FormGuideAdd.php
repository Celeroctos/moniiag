<?php

class FormGuideAdd extends CFormModel
{
    public $name;
    public $id;

    public function rules()
    {
        return array(
            array(
                'name', 'required'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Название справочника',
        );
    }
}


?>