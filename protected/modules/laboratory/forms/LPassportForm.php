<?php

class LPassportForm extends LFormModel {

	public $id;
	public $series;
	public $number;
	public $subdivision_name;
	public $issue_date;
	public $subdivision_code;

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
			"id" => [
				"label" => "Идентификатор",
				"type" => "number"
			],
			"series" => [
				"label" => "Серия",
				"type" => "number",
				"rules" => "required"
			],
			"number" => [
				"label" => "Номер",
				"type" => "number",
				"rules" => "required"
			],
			"subdivision_name" => [
				"label" => "Название подразделения",
				"type" => "text",
				"rules" => "required"
			],
			"issue_date" => [
				"label" => "Дата выдачи",
				"type" => "date",
				"rules" => "required"
			],
			"subdivision_code" => [
				"label" => "Код подразделения",
				"type" => "number",
				"rules" => "required"
			]
		];
	}
}