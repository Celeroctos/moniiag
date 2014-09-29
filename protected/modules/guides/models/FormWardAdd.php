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
            ),
			array(
				'id', 'safe'
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