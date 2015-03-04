<?php

class LMedcardSearchForm extends LFormModel {

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
            "card_number" => [
                "label" => "Номер ЛКП",
                "type" => "text"
            ],
            "enterprise_id" => [
                "label" => "Направитель",
                "type" => "DropDown"
            ],
            "phone" => [
                "label" => "Телефон",
                "type" => "Phone",
                "rules" => "LPhoneValidator"
            ],
            "first_name" => [
                "label" => "Фамилия",
                "type" => "text"
            ],
            "middle_name" => [
                "label" => "Имя",
                "type" => "text"
            ],
            "last_name" => [
                "label" => "Отчество",
                "type" => "text"
            ]
        ];
    }

	public function getEnterpriseId() {
		return CHtml::listData(Enterprise::model()->findAll(), "id", "shortname");
	}
}