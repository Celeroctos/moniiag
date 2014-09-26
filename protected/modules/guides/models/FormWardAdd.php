<?php

class FormWardAdd extends CFormModel
{
    public $name;
    public $enterprise;
	public $ruleId;
    public $id;

    public function rules()
    {
        return array(
            array(
                'name, enterprise, ruleId', 'required'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'name'=> 'Название отделения',
            'enterprise' => 'Учреждение',
			'ruleId' => 'Правило генерации номера карты'
        );
    }
}


?>