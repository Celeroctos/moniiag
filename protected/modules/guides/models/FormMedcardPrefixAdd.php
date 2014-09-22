<?php
class FormMedcardPrefixAdd extends FormMisDefault
{
    public $value;
    public $id;

    public function rules()
    {
        return array(
			array(
				'value', 'required'
			)
        );
    }


    public function attributeLabels()
    {
        return array(
			'value' => 'Префикс'
        );
    }
}
?>