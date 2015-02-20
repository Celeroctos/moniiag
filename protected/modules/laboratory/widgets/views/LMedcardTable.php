<?php
/**
 * @var LMedcardTable $this - Self instance
 */

$this->widget("LTable", [
	"widget" => $this,
    "table" => new LMedcard(),
    "header" => [
        "number" => [
            "label" => "Номер ЛКП",
			"style" => "width: 15%"
        ],
        "fio" => [
            "label" => "ФИО пациента",
			"style" => "width: 30%"
        ],
        "charged_by" => [
            "label" => "МУ направитель",
			"style" => "width: 15%"
        ],
        "birthday" => [
            "label" => "Дата рождения",
			"style" => "width: 15%"
        ],
        "phone" => [
            "label" => "Контактный телефон"
        ]
    ],
    "pk" => "number",
	"sort" => "number",
	"id" => "medcard-table"
]);