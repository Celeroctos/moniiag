<?php

class LSearchRangeForm extends LFormModel {

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
            "begin_date" => [
                "label" => "С...",
                "type" => "date"
            ],
            "end_date" => [
                "label" => "По...",
                "type" => "date"
            ]
        ];
    }
}