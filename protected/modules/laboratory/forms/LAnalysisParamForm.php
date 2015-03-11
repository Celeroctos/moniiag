<?php

class LAnalysisParamForm extends LFormModel {

	/**
	 * Override that method to return additional rule configuration, like
	 * scenario conditions or others
	 * @return array - Array with rule configuration
	 */
	public function backward() {
		return [

			// don't validate 'id' on register
			[ "id", "required", "on" => [ "update", "search" ] ],

			// set maximum name length
			[ "name", "length", "max" => 30 ],

			// set maximum long name length
			[ "long_name", "length", "max" => 200 ]
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
				"type" => "text"
			],
			"name" => [
				"label" => "Наименование",
				"type" => "text",
				"rules" => "required"
			],
			"long_name" => [
				"label" => "Описание",
				"type" => "TextArea",
				"rules" => "safe"
			],
			"comment" => [
				"label" => "Комментарий",
				"type" => "TextArea",
				"rules" => "safe"
			]
		];
	}
}