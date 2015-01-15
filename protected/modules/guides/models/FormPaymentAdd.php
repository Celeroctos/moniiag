<?php

class FormPaymentAdd extends FormMisDefault
{
    public $name;
    public $tasuString;
	public $id;
	public $isDefault;

    public function rules()
    {
        return array(
            array(
                'name, tasuString', 'required'
            ),
            array(
                'id, isDefault', 'numerical'
            ),
			array(
				'id', 'safe'
			)
        );
    }


    public function attributeLabels()
    {
        return array(
            'id' => 'Код',
            'name' => 'Название',
            'tasuString' => 'Строка для ТАСУ',
			'isDefault' => 'По умолчанию или нет'
        );
    }
}


?>