<?php

class LDirectionForm extends LFormModel {

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
			"barcode" => [
				"label" => "Штрих-код",
				"type" => "number",
				"rules" => "required"
			],
            "status" => [
                "label" => "Статус",
                "type" => "DirectionStatus",
                "rules" => "required"
            ],
			"analysis_type_id" => [
				"label" => "Тип анализа",
				"type" => "DropDown",
				"rules" => "required",
                "table" => [
                    "name" => "lis.analysis_types",
                    "key" => "id",
                    "value" => "name"
                ]
			],
            "card_number" => [
                "label" => "Номер карты",
                "type" => "text"
            ],
            "history" => [
                "label" => "Медикаментозный анамнез",
                "type" => "TextArea"
            ],
			"comment" => [
				"label" => "Комментарий",
				"type" => "TextArea"
			],
			"sender_id" => [
				"label" => "Врач",
				"type" => "number",
				"rules" => "required",
                "value" => Yii::app()->user->getState("doctorId"),
                "hidden" => "true"
			],
            "department_id" => [
                "label" => "Направитель",
                "type" => "DropDown",
                "rules" => "required",
                "table" => [
                    "name" => "mis.enterprise_params",
                    "key" => "id",
                    "value" => "shortname"
                ]
            ],
			"ward_id" => [
				"label" => "Отдел",
				"type" => "DropDown",
				"rules" => "required",
                "table" => [
                    "name" => "mis.wards",
                    "key" => "id",
                    "value" => "name"
                ]
			],
			"sending_date" => [
				"label" => "Дата направления",
				"type" => "date",
				"rules" => "required"
			],
			"treatment_room_employee_id" => [
				"label" => "Сотрудник процедурного кабинета",
				"type" => "DropDown",
				"rules" => "required",
                "table" => [
                    "format" => "%{first_name} %{middle_name} %{last_name}",
                    "name" => "mis.doctors",
                    "key" => "id",
                    "value" => "first_name, middle_name, last_name"
                ]
			],
			"laboratory_employee_id" => [
				"label" => "Сотрудник лаборатории",
				"type" => "DropDown",
				"rules" => "required",
                "table" => [
                    "format" => "%{first_name} %{middle_name} %{last_name}",
                    "name" => "mis.doctors",
                    "key" => "id",
                    "value" => "first_name, middle_name, last_name"
                ]
			]
		];
	}
}