<?php

class FormInsuranceAdd extends FormMisDefault
{
    public $name;
    public $id;
    public $regionsHidden;
	public $code;


    public function rules()
    {
        return array(
            array(
                'name, code', 'required'
            ),
            array(
                'regionsHidden', 'safe'
            )
        );
    }


    public function attributeLabels()
    {
        return array(
            'name'=> 'Название',
			'code' => 'Код СМО'
        );
    }
}


?>