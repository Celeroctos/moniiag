<?php

class FormSystemEdit extends FormMisDefault
{
    public $lettersInPixel;
	public $tasuMode;
	public $sessionStandbyTime;

    public function rules()
    {
        return array(
            array(
                'lettersInPixel, tasuMode, sessionStandbyTime', 'required'
            ),
            array(
                'lettersInPixel, tasuMode, sessionStandbyTime', 'numerical'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'lettersInPixel' => 'Количество пикселей в символе',
			'tasuMode' => 'ТАСУ включена',
			'sessionStandbyTime' => 'Время простоя пользовательской сессии (секунд)'
        );
    }
}


?>