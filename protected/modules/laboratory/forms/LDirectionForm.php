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
            "history" => [
                "label" => "Медикаментозный анамнез",
                "type" => "TextArea"
            ],
			"comment" => [
				"label" => "Комментарий",
				"type" => "TextArea"
			],
			"ward_id" => [
				"label" => "Отдел",
				"type" => "DropDown",
				"rules" => "required"
			],
			"sending_date" => [
				"label" => "Дата направления",
				"type" => "date",
				"rules" => "required",
				"value" => date("Y-m-d")
			],
			"treatment_root_employee_id" => [
				"label" => "Сотрудник процедурного кабинета",
				"type" => "DropDown",
				"rules" => "required"
			],
			"laboratory_employee_id" => [
				"label" => "Сотрудник лаборатории",
				"type" => "DropDown",
				"rules" => "required"
			]
		];
	}

	public function getWardIdData() {
        return $this->getWards();
	}

    public function getTreatmentRootEmployeeIdData() {
        return $this->getDoctors();
    }

    public function getLaboratoryEmployeeIdData() {
        return $this->getDoctors();
    }

    private function getWards() {
        if (!$this->wards) {
            return ($this->wards = CHtml::listData(Ward::model()->getAll(), "id", "name"));
        } else {
            return $this->wards;
        }
    }

    private function getDoctors() {
        if (!$this->doctors) {
            return ($this->doctors = CHtml::listData(Doctor::model()->getAll(), "id", "fio"));
        } else {
            return $this->doctors;
        }
    }

    private $wards = null;
    private $doctors = null;
}