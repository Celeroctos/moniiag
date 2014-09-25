<?php
class FormMedcardRuleAdd extends FormMisDefault
{
    public $prefixId;
	public $postfixId;
	public $parentId;
	public $typeId;
	public $participleModePrefix;
	public $participleModePostfix;
	public $name;
    public $id;

    public function rules()
    {
        return array(
			array(
				'typeId, name', 'required'
			),
			array(
				'parentId, postfixId, prefixId, typeId, participleModePrefix, participleModePostfix', 'numerical'
			)
        );
    }


    public function attributeLabels()
    {
        return array(
			'name' => 'Название',
			'typeId' => 'Правило формирования номера',
			'parentId' => 'Унаследован от правила',
			'prefixId' => 'Префикс',
			'postfixId' => 'Постфикс',
			'participleModePrefix' => 'Предыдущий префикс',
			'participleModePostfix' => 'Предыдущий постфикс'
        );
    }
}
?>