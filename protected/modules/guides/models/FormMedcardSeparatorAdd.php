<?php
class FormMedcardSeparatorAdd extends FormMisDefault
{
	public $value;
    public $id;

    public function rules()
    {
        return array(
			array(
				'id, value', 'required'
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