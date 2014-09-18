<?php
class FormMedcardRuleAdd extends FormMisDefault
{
    public $prefixId;
	public $postfixId;
	public $parentId;
	public $typeId;
    public $id;

    public function rules()
    {
        return array(
			array(
				'typeId', 'required'
			),
			array(
				'parentId, postfixId, prefixId, typeId', 'numerical'
			)
        );
    }


    public function attributeLabels()
    {
        return array(
			'typeId' => 'Правило формирования номера',
			'parentId' => 'Унаследован от правила',
			'prefixId' => 'Префикс',
			'postfixId' => 'Постфикс',
        );
    }
}
?>