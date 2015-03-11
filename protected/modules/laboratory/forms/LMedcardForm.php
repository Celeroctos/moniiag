<?php

class LMedcardForm extends LFormModel {

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
			"card_number" => [
				"label" => "Номер карты",
				"type" => "text",
				"rules" => "required"
			],
			"mis_medcard" => [
				"label" => "Идентификатор в МИС",
				"type" => "number",
				"hidden" => "true"
			],
			"sender_id" => [
				"label" => "Врач направитель",
				"type" => "DropDown",
				"table" => [
					"name" => "mis.doctors",
					"key" => "id",
					"value" => "surname, name",
					"format" => "%{surname} %{name}"
				]
			],
			"patient_id" => [
				"label" => "Информаия о пациенте",
				"type" => "form",
				"rules" => "required",
				"form" => "LPatientForm"
			]
		];
	}
}