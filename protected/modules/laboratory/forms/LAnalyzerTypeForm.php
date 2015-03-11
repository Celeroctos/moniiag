<?php

class LAnalyzerTypeForm extends LFormModel {

	/**
	 * Override that method to return additional rule configuration, like
	 * scenario conditions or others
	 * @return array - Array with rule configuration
	 */
	public function backward() {
		return [

			// don't validate identification number on register
			[ "id", "required", "on" => [ "update", "search" ] ],

			// set maximum length of type and name fields
			[ ["type", "name"], "length", "max" => 100 ]
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
				"type" => "number"
			],
			"type" => [
				"label" => "Название типа анализатора",
				"type" => "text",
				"rules" => "required"
			],
			"name" => [
				"label" => "Название анализатора",
				"type" => "text",
				"rules" => "required"
			],
			"notes" => [
				"label" => "Пометки",
				"type" => "TextArea",
				"rules" => "safe"
			]
		];
	}
}