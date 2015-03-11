<?php

class LAnalysisSampleTypeForm extends LFormModel {

	/**
	 * Override that method to return additional rule configuration, like
	 * scenario conditions or others
	 * @return array - Array with rule configuration
	 */
	public function backward() {
		return [

			// don't validate identification number on register
			[ "id", "required", "on" => [ "update", "search" ] ],
			
			// set maximum length of type and subtype
			[ ["type", "subtype"], "length", "max" => 100 ]
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
				"label" => "Тип образца",
				"type" => "text",
				"rules" => "required"
			],
			"subtype" => [
				"label" => "Подтип образца",
				"type" => "text",
				"rules" => "required"
			]
		];
	}
}