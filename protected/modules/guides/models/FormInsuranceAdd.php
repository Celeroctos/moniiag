<?php

class FormInsuranceAdd extends FormMisDefault
{
    public $name;
    public $id;
    public $regionsHidden;


    public function rules()
    {
        return array(
            array(
                'name', 'required'
            ),
            array(
                'regionsHidden', 'safe'
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