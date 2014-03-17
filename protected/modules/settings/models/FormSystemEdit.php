<?php

class FormSystemEdit extends FormMisDefault
{
    public $lettersInPixel;

    public function rules()
    {
        return array(
            array(
                'lettersInPixel', 'required'
            ),
            array(
                'lettersInPixel', 'numerical'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'lettersInPixel' => 'Количество пикселей в символе'
        );
    }
}


?>