<?php

class LPatientForm extends LFormModel {

	/**
	 * Override that method to return additional rule configuration, like
	 * scenario conditions or others
	 * @return array - Array with rule configuration
	 */
	public function backward() {
		return [
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
				"label" => "Идентификатор",
				"type" => "number",
				"rules" => "safe, numerical"
			],
			"surname" => [
				"label" => "Фамилия",
				"type" => "text",
				"rules" => "required"
			],
			"name" => [
				"label" => "Имя",
				"type" => "text",
				"rules" => "required"
			],
			"patronymic" => [
				"label" => "Отчество",
				"type" => "text",
				"rules" => "safe"
			],
			"sex" => [
				"label" => "Пол",
				"type" => "sex",
				"rules" => "required"
			],
			"birthday" => [
				"label" => "Дата рождения",
				"type" => "date",
				"rules" => "required"
			],
			"policy_number" => [
				"label" => "Номер полиса",
				"type" => "text",
				"rules" => "safe"
			],
			"policy_issue_date" => [
				"label" => "Дата выдачи полиса",
				"type" => "date",
				"rules" => "safe"
			],
			"policy_insurance_id" => [
				"label" => "СМО, выдавшая полис",
				"type" => "text",
				"rules" => "safe",
				"table" => [
					"name" => "mis.insurances",
					"key" => "id",
					"value" => "name"
				]
			],
			"register_address_id" => [
				"label" => "Адрес регистрации",
				"type" => "number",
				"rules" => "safe",
				"form" => "LAddressForm"
			],
			"address_id" => [
				"label" => "Адрес фактического проживания",
				"type" => "number",
				"rules" => "safe",
				"form" => "LAddressForm"
			],
			"is_policy_voluntary" => [
				"label" => "Является ДМС?",
				"type" => "YesNo",
				"rules" => "required"
			]
		];
	}
}