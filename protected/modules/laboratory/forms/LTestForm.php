<?php
/**
 * Created by PhpStorm.
 * User: Savonin
 * Date: 2015-03-07
 * Time: 00:01
 */

class LTestForm extends LFormModel {

	/**
	 * Override that method to return additional rule configuration, like
	 * scenario conditions or others
	 * @return array - Array with rule configuration
	 */
	public function backward() {
		return [

			// don't validate 'id' on register
			[ "id", "required", "on" => [ "update", "search" ] ],

			// set maximum name length to 50
			[ "name", "length", "max" => 50 ]
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
			"doctor_id" => [
				"label" => "Врач",
				"type" => "DropDown",
				"rules" => "required",
				"table" => [
					"name" => "mis.doctors",
					"format" => "%{first_name} %{last_name}",
					"key" => "id",
					"value" => "first_name, middle_name, last_name"
				]
			],
			"name" => [
				"label" => "Наименование",
				"type" => "text",
				"rules" => "required"
			]
		];
	}
}