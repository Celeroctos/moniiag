<?php

class LGuideColumnForm extends LFormModel {

	public $id;
	public $name;
	public $type;
	public $guide_id;
	public $lis_guide_id;
	public $position;
	public $display_id;
	public $default_value;

	/**
	 * Override that method to return config. Config should return array associated with
	 * model's variables. Every field must contains 3 parameters:
	 *  + label - Variable's label, will be displayed in the form
	 *  + type - Input type (@see _LFormInternalRender#render())
	 *  + rules - Basic form's Yii rules, such as 'required' or 'numeric' etc
	 * @return Array - Model's config
	 */
	public function config() {
		$guides = LGuide::model()->findForDropDown();
		if ($this->isActive("lis_guide_id")) {
			$columns = LGuideColumn::model()->findDisplayableAndOrdered("guide_id = :guide_id", [
				$this->lis_guide_id
			]);
			$columns = LGuideColumn::model()->toDropDown($columns);
		} else {
			$columns = [];
		}
		return [
			"id" => [
				"label" => "Идентификатор",
				"type" => "number",
				"rules" => "required",
				"hidden" => "true"
			],
			"name" => [
				"label" => "Название столбца",
				"type" => "Text",
				"rules" => "required"
			],
			"type" => [
				"label" => "Тип данных",
				"type" => "DropDown",
				"rules" => "required",
				"data" => LFieldCollection::getCollection()->getDropDown([
					"Text",
					"TextArea",
					"Number",
					"YesNo",
					"DropDown",
					"Multiple",
					"Date"
				]),
				"update" => "default_value"
			],
			"guide_id" => [
				"label" => "Справочник",
				"type" => "DropDown",
				"rules" => "required",
				"data" => $guides,
				"format" => "%{name}",
				"hidden" => "true"
			],
			"lis_guide_id" => [
				"label" => "Справочник",
				"type" => "DropDown",
				"data" => $guides,
				"format" => "%{name}",
				"hidden" => $this->hasListType(),
				"update" => "display_id"
			],
			"position" => [
				"label" => "Позиция",
				"type" => "Number",
				"hidden" => "true",
				"options" => [
					"min" => 1
				]
			],
			"display_id" => [
				"label" => "Отображаемое значение",
				"type" => "DropDown",
				"data" => $columns,
				"format" => "%{name}",
				"hidden" => $this->hasListType()
			],
			"default_value" => [
				"label" => "Значение по умолчанию",
				"type" => $this->isActive("type") ? $this->type : "hidden",
				"value" => $this->default_value
			]
		];
	}

	private function hasListType() {
		return !$this->isActive("type") || ($this->type != "dropdown" && $this->type != "multiple");
	}
}