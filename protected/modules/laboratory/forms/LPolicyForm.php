<?php

class LPolicyForm extends LFormModel {

	/**
	 * Override that method to return additional rule configuration, like
	 * scenario conditions or others
	 * @return array - Array with rule configuration
	 */
	public function backward() {
		return [
			$this->createFilter("treatment.policy", [
				"region",
				"insurance",
				"oms_number",
				"type",
				"givedate",
				"status"
			])
		];
	}

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
				"label" => "Первичный ключ",
				"type" => "number"
			],
			"last_name" => [
				"label" => "Фамилия",
				"type" => "text",
				"rules" => "required"
			],
			"first_name" => [
				"label" => "Имя",
				"type" => "text",
				"rules" => "required"
			],
			"middle_name" => [
				"label" => "Отчество",
				"type" => "text"
			],
			"oms_number" => [
				"label" => "Номер ОМС",
				"type" => "text",
				"rules" => "required"
			],
			"gender" => [
				"label" => "Пол",
				"type" => "sex",
				"rules" => "required"
			],
			"birthday" => [
				"label" => "Дата рождения",
				"type" => "date",
				"rules" => "required"
			],
			"type" => [
				"label" => "Тип полиса",
				"type" => "DropDown",
				"rules" => "required",
				"table" => [
					"name" => "mis.oms_types",
					"key" => "id",
					"value" => "name"
				]
			],
			"givedate" => [
				"label" => "Дата выдачи",
				"type" => "date",
				"rules" => "required"
			],
			"status" => [
				"label" => "Статус",
				"type" => "DropDown",
				"rules" => "required",
				"table" => [
					"name" => "mis.oms_statuses",
					"key" => "id",
					"value" => "name"
				]
			],
			"insurance" => [
				"label" => "Страховая компания",
				"type" => "DropDown",
				"rules" => "required",
				"table" => [
					"name" => "mis.insurances",
					"key" => "id",
					"value" => "name"
				]
			],
			"region" => [
				"label" => "Регион",
				"type" => "text"
			],
			"oms_series" => [
				"label" => "Серия",
				"type" => "text",
				"rules" => "required"
			],
			"oms_series_number" => [
				"label" => "Серия и номер ОМС (дефисы, пробелы)",
				"type" => "text",
				"hidden" => "true"
			]
		];
	}
}