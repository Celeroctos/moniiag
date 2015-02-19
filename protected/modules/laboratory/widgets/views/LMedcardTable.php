<?php
/**
 * @var LMedcardTable $this - Self instance
 */

$this->widget("LTable", [
    "table" => new LMedcard(),
    "header" => [
        "number" => [
            "label" => "Номер ЛКП"
        ],
        "fio" => [
            "label" => "ФИО пациента"
        ],
        "charged_by" => [
            "label" => "МУ направитель"
        ],
        "birthday" => [
            "label" => "Дата рождения"
        ],
        "phone" => [
            "label" => "Контактный телефон"
        ]
    ],
    "pk" => "number",
    "disableControl" => "true"
]);