<?php

class FormChooseEmployee extends FormMisDefault
{
    public $id;

    public function rules()
    {
        return array(
            array(
                'id', 'required'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'Сотрудник'
        );
    }
}


?>