<?php

class TestForm extends LFormModel {

    /**
     * Override that method to return model configuration
     * @return array - Model
     */
    public function config() {
        return [
            "name" => [
                "label" => "Название категории",
                "type" => "Text",
                "rules" => "required"
            ],
            "parentId" => [
                "label" => "Категория-родитель",
                "type" => "DropDown",
                "rules" => "required, numerical",
                "format" => "%{id} - %{name} (%{path})",
                "data" => MedcardCategorie::model()->findAll()
            ],
            "position" => [
                "label" => "Позиция среди сестринских категорий и элементов",
                "type" => "Number",
                "rules" => "required, numerical"
            ],
            "isDynamic" => [
                "label" => "Возможность динамического добавления в медкарту",
                "type" => "YesNo",
                "rules" => "required, numerical"
            ]
        ];
    }
}