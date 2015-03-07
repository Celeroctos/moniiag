<?php
/**
 * Created by PhpStorm.
 * User: Savonin
 * Date: 2015-03-06
 * Time: 18:02
 */

class LAnalyzerTypeAnalysisForm extends LFormModel {

	/**
	 * Override that method to return additional rule configuration, like
	 * scenario conditions or others
	 * @return array - Array with rule configuration
	 */
	public function backward() {
		return [
			[ "id", "required", "on" => [ "update", "search" ] ]
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
			"analyzer_type_id" => [
				"label" => "Тип анализатора",
				"type" => "DropDown",
				"rules" => "required",
				"table" => [
					"name" => "lis.analyzer_types",
					"key" => "id",
					"value" => "name"
				]
			],
			"analysis_type_id" => [
				"label" => "Тип анализа",
				"type" => "DropDown",
				"rules" => "required",
				"table" => [
					"name" => "lis.analysis_types",
					"key" => "id",
					"value" => "short_name"
				]
			]
		];
	}
}