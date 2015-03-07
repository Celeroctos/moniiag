<?php
/**
 * Created by PhpStorm.
 * User: Savonin
 * Date: 2015-03-06
 * Time: 17:58
 */

class LAnalysisTypeForm extends LFormModel {

	/**
	 * Override that method to return additional rule configuration, like
	 * scenario conditions or others
	 * @return array - Array with rule configuration
	 */
	public function backward() {
		return [

			// don't validate identification number on register
			[ "id", "required", "on" => [ "update", "search" ] ],

			// set maximum length of name field
			[ "name", "length", "max" => 200 ],

			// set maximum length of short name field
			[ "short_name", "length", "max" => 20 ]
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
			"name" => [
				"label" => "Наименование",
				"type" => "text",
				"rules" => "required"
			],
			"short_name" => [
				"label" => "Краткое наименование анализа",
				"type" => "text",
				"rules" => "required"
			],
			"automatic" => [
				"label" => "Ручная методика",
				"type" => "YesNo",
				"rules" => "required"
			],
			"manual" => [
				"label" => "Автоматическая методика",
				"type" => "YesNo",
				"rules" => "required"
			]
		];
	}
}