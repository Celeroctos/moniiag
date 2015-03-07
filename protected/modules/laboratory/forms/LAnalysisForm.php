<?php

class LAnalysisForm extends LFormModel {

	/**
	 * Override that method to return additional rule configuration, like
	 * scenario conditions or others
	 * @return array - Array with rule configuration
	 */
	public function backward() {
		return [

			// don't validate identification number no register
			[ "id", "required", "on" => [ "update", "search" ] ],

			// hide primary key on register
			[ "id", "hidden", "on" => "register" ],

			// maximum length of medcard number
			[ "medcard_number", "length", "max" => 50 ]
		];
	}

	/**
	 * Override that method to return config. Config should return array associated with
	 * model's variables. Every field must contains 3 parameters:
	 *  + label - Variable's label, will be displayed in the form
	 *  + type - Input type, check out field folder and it's abstract classes
	 *  + rules - Basic form's Yii rules, such as 'required' or 'numeric' etc
	 * @return Array - Model's config
	 * @see LField, LDropDown
	 */
	public function config() {
		return [
			"id" => [
				"label" => "Идентификатор",
				"type" => "number"
			],
			"registration_date" => [
				"label" => "Дата регистрации",
				"type" => "date",
				"rules" => "safe",
				"hidden" => "true"
			],
			"direction_id" => [
				"label" => "Направление",
				"type" => "DropDown",
				"rules" => "required",
				"table" => [
					"name" => "lis.direction",
					"key" => "id",
					"value" => "barcode"
				]
			],
			"doctor_id" => [
				"label" => "Врач",
				"type" => "DropDown",
				"rules" => "required",
				"table" => [
					"name" => "mis.doctors",
					"format" => "%{first_name} %{last_name}",
					"key" => "id",
					"value" => "first_name, last_name"
				]
			],
			"medcard_number" => [
				"label" => "Номер ЛКП",
				"type" => "text",
				"rules" => "required"
			]
		];
	}
}