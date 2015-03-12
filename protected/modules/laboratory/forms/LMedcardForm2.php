<?php

class LMedcardForm2 extends LFormModel {

	/**
	 * Override that method to return additional rule configuration, like
	 * scenario conditions or others
	 * @return array - Array with rule configuration
	 */
	public function backward() {
		return [
			$this->createFilter("treatment.show", [
				"work_place",
				"work_address",
				"post",
				"profession",
				"snils",
				"invalid_group",
				"privelege_code",
				"contact"
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
			"work_place" => [
				"label" => "Место работы",
				"type" => "text"
			],
			"work_address" => [
				"label" => "Адрес работы",
				"type" => "text"
			],
			"post" => [
				"label" => "Должность",
				"type" => "text"
			],
			"profession" => [
				"label" => "Профессия",
				"type" => "text"
			],
			"snils" => [
				"label" => "СНИЛС",
				"type" => "text"
			],
			"invalid_group" => [
				"label" => "Группа инвалидности",
				"type" => "DisabilityGroup"
			],
			"privelege_code" => [
				"label" => "Привилегия",
				"type" => "DropDown",
				"table" => [
					"name" => "mis.privileges",
					"key" => "id",
					"value" => "name"
				]
			],
			"contact" => [
				"label" => "Телефон",
				"type" => "text"
			],
		];
	}
}