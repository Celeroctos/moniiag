<?php

class LGuideValueForm extends LFormModel {

	public $id;
	public $guide_row_id;
	public $guide_column_id;
	public $value;

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
			"guide_row_id" => [
				"label" => "Строка",
				"type" => "number",
				"data" => LGuideColumn::model()->findForDropDown(),
				"format" => "%{id}"
			],
			"guide_column_id" => [
				"label" => "Столбец",
				"type" => "number",
				"data" => LGuideColumn::model()->findForDropDown(),
				"format" => "%{name}"
			],
			"value" => [
				"label" => "Значение",
				"type" => isset($this->type) ? $this->type : "Text",
				"rules" => "required"
			]
		];
	}
}