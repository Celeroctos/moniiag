<?php

class FormSystemEdit extends FormMisDefault
{
    public $lettersInPixel;
	public $tasuMode;

    public function rules()
    {
        return array(
            array(
                'lettersInPixel, tasuMode', 'required'
            ),
            array(
                'lettersInPixel, tasuMode', 'numerical'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'lettersInPixel' => 'Количество пикселей в символе',
			'tasuMode' => 'ТАСУ включена'
        );
    }
}


?>