<?php

class LAddressForm extends LFormModel {

	/**
	 * Override that method to return additional rule configuration, like
	 * scenario conditions or others
	 * @return array - Array with rule configuration
	 */
	public function backward() {
		return [
			[ "street_name", "length", "max" => 200 ],
			[ [ "house_number", "flag_number" ], "length", "max" => 10 ],
			[ "city", "length", "max" => 50 ]
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
			"street_name" => [
				"label" => "Название улицы",
				"type" => "text",
				"rules" => "required"
			],
			"house_number" => [
				"label" => "Номер дома",
				"type" => "text",
				"rules" => "required"
			],
			"flat_number" => [
				"label" => "Номер квартиры",
				"type" => "text",
				"rules" => "required"
			],
			"post_index" => [
				"label" => "Почтовый индекс",
				"type" => "text",
				"rules" => "safe"
			],
			"city" => [
				"label" => "Город",
				"type" => "text",
				"rules" => "required"
			]
		];
	}
}