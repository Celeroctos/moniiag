<?php

class LGuideValueForm extends LFormModel {

	/**
	 * Override that method to return config. Config should return array associated with
	 * model's variables. Every field must contains 3 parameters:
	 *  + label - Variable's label, will be displayed in the form
	 *  + type - Input type (@see _LFormInternalRender#render())
	 *  + rules - Basic form's Yii rules, such as 'required' or 'numeric' etc
	 * @return Array - Model's config
	 */
	public function config() {
		return [
			"type" => [
				"label" => "Тип данных",
				"type" => "DropDown",
				"rules" => "required",
				"data" => LFieldCollection::getCollection()->getDropDown()
			],
			"value" => [
				"label" => "Значение",
				"type" => isset($this->type) ? $this->type : "Text",
				"rules" => "required",
				"dependence" => "type"
			],
			"guide_id" => [
				"label" => "Справочник",
				"type" => "DropDown",
				"rules" => "required",
				"data" => LGuide::model()->findAll(),
				"format" => "%{name}"
			],
			"guide_column_id" => [
				"label" => "Столбец",
				"type" => "DropDown",
				"dependence" => "guide_id",
				"rules" => "required",
				"data" => isset($this->guide_id) ? LGuideColumn::model()->findAll(
						"guide_id = :guide_id", [
							":guide_id" => $this->guide_id
						]
					) : [],
				"format" => "%{name}"
			]
		];
	}
}