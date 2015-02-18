<?php

class LMedcardForm extends LFormModel {

	public $id;
	public $surname;
	public $name;
	public $patronymic;
	public $sex;
	public $birthday;
	public $mis_patient_id;
	public $charge_person;
	public $number;

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
				"hidden" => "true"
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
				"type" => "text"
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
			"mis_patient_id" => [
				"label" => "Идентификатор пациента в МИС",
				"type" => "number"
			],
			"charge_person" => [
				"label" => "Направитель пациента",
				"type" => "text"
			],
			"number" => [
				"label" => "Номер ЛКП",
				"type" => "number"
			],
			"policy" => [
				"label" => "Номер полиса ОМС",
				"type" => "text"
			],
			"policy_region" => [
				"label" => "Регион выдачи полиса ОМС",
				"type" => "text"
			],
			"policy_register_date" => [
				"label" => "Дата выдачи СМО",
				"type" => "date"
			],
			"passport_series" => [
				"label" => "Серия паспорта",
				"type" => "number"
			],
			"passport_number" => [
				"label" => "Номер паспорта",
				"type" => "number"
			],
			"snils" => [
				"label" => "Номер СНИЛС",
				"type" => "number"
			],
			"phone" => [
				"label" => "Контактный телефон",
				"type" => "text",
				"rules" => "LPhoneValidator"
			],
			"address" => [
				"label" => "Адрес регистрации",
				"type" => "text"
			],
			"register_address" => [
				"label" => "Адрес фактического проживания",
				"type" => "text"
			]
		];
	}
}