<?php

class LMedcardForm extends LFormModel {

	public $privilege_code;
	public $snils;
	public $address;
	public $address_reg;
	public $doctype;
	public $serie;
	public $docnumber;
	public $gived_date;
	public $contact;
	public $invalid_group;
	public $card_number;
	public $enterprise_id;
	public $policy_id;
	public $reg_date;
	public $work_place;
	public $work_address;
	public $post;
	public $profession;
	public $motion;
	public $address_str;
	public $address_reg_str;
	public $user_created;

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
			"privilege_code" => [
				"label" => "Код привилегии",
				"type" => "number",
				"hidden" => "true"
			],
			"snils" => [
				"label" => "Код СНИСЛ",
				"type" => "text",
				"rules" => "required"
			],
			"address" => [
				"label" => "Адрес фактического проживания",
				"type" => "text",
				"rules" => "required"
			],
			"address_reg" => [
				"label" => "Адрес регистрации",
				"type" => "text",
				"rules" => "required"
			],
			"doctype" => [
				"label" => "Тип документа",
				"type" => "DropDown",
				"rules" => "required"
			],
			"serie" => [
				"label" => "Серия паспорта",
				"type" => "number",
				"rules" => "required"
			],
			"docnumber" => [
				"label" => "Номер паспорта",
				"type" => "number",
				"rules" => "required"
			],
			"gived_date" => [
				"label" => "Дата выдачи",
				"type" => "date",
				"rules" => "required",
				"value" => date("Y-m-d")
			],
			"contact" => [
				"label" => "Контакты",
				"type" => "text"
			],
			"invalid_group" => [
				"label" => "Группа инвалидности",
				"type" => "DropDown",
				"data" => [
					0 => "Группа 0",
					1 => "Группа 1",
					2 => "Группа 2"
				]
			],
			"card_number" => [
				"label" => "Номер карты",
				"type" => "text",
				"rules" => "required"
			],
			"enterprise_id" => [
				"label" => "Заведение",
				"type" => "DropDown",
				"rules" => "required"
			],
			"policy_id" => [
				"label" => "Номер полиса ОМС",
				"type" => "search",
				"rules" => "required"
			],
			"reg_date" => [
				"label" => "Дата регистрации карты",
				"type" => "date",
				"rules" => "required",
				"value" => date("Y-m-d")
			],
			"work_place" => [
				"label" => "Место работы",
				"type" => "text"
			],
			"work_address" => [
				"label" => "Адрес работы",
				"type" => "text"
			] ,
			"post" => [
				"label" => "Должность на работе",
				"type" => "text"
			],
			"profession" => [
				"label" => "Профессия",
				"type" => "text"
			],
			"motion" => [
				"label" => "Статус продвижения медкарты",
				"type" => "DropDown",
				"data" => [
					0 => "По умолчанию"
				]
			],
			"address_str" => [
				"label" => "Строковое представление адреса проживания для поиска",
				"type" => "text",
				"hidden" => "true"
			],
			"address_reg_str" => [
				"label" => "Строковое представление адреса регистрации для поиска",
				"type" => "text",
				"hidden" => "true"
			],
			"user_created" => [
				"label" => "Зарегестрировал пользователь",
				"type" => "number",
				"hidden" => "true",
				"value" => Yii::app()->user->id
			]
		];
	}

	public function getDoctype() {
		return CHtml::listData(Doctype::model()->findAll(), "id", "name");
	}

	public function getEnterpriseId() {
		return CHtml::listData(Enterprise::model()->findAll(), "id", "shortname");
	}
}