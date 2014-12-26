<?php

class FormServiceAdd extends CFormModel
{
    public $name;
    public $code;
	public $isDefault;
    public $id;

    public function rules()
    {
        return array(
            array(
                'name, code, isDefault', 'required'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Описание услуги',
            'code' => 'Код в ТАСУ',
			'isDefault' => 'Значение по умолчанию?'
        );
    }
}


?>