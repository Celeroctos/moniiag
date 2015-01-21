<?php

class TestForm extends LComponent {

    /**
     * Override that method to return model configuration
     * @return array - Model
     */
    public function model() {
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
                "data" => [
                    0 => "0",
                    1 => "1",
                    2 => "2",
                    3 => "3"
                ]
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

    /**
     * Override that method to return view configuration
     * @return mixed - View
     */
    public function view() {
        return [
            "title" => "Тестовая форма",
            "id" => "test-form",
            "url" => "/admin/categories/add"
        ];
    }
}