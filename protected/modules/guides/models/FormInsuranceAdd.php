<?php

class FormInsuranceAdd extends FormMisDefault
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
            'name'=> 'Название'
        );
    }
}


?>