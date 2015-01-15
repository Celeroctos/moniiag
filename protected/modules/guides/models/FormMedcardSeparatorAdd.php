<?php
class FormMedcardSeparatorAdd extends FormMisDefault
{
	public $value;
    public $id;

    public function rules()
    {
        return array(
			array(
				'value', 'required'
			),
			array(
				'id', 'safe'
			)
        );
    }


    public function attributeLabels()
    {
        return array(
			'value' => 'Разделитель'
        );
    }
}
?>