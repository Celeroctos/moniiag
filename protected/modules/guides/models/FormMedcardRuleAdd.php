<?php
class FormMedcardRuleAdd extends FormMisDefault
{
    public $prefixId;
	public $postfixId;
	public $prefixSeparatorId;
	public $postfixSeparatorId;
	public $parentId;
	public $typeId;
	public $participleModePrefix;
	public $participleModePostfix;
	public $cardType; // Тип карты 
	public $name;
    public $id;

    public function rules()
    {
        return array(
			array(
				'typeId, name', 'required'
			),
			array(
				'parentId, postfixId, prefixId, typeId, participleModePrefix, participleModePostfix, postfixSeparatorId, prefixSeparatorId, cardType', 'numerical'
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
			'prefixSeparatorId' => 'Разделитель префикса',
			'postfixSeparatorId' => 'Разделитель постфикса',
			'participleModePrefix' => 'Предыдущий префикс',
			'participleModePostfix' => 'Предыдущий постфикс',
			'cardType' => 'Тип карты'
        );
    }
}
?>