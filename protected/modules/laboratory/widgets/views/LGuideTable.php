<?php

/**
 * @var LWidget $this
 */

$this->widget("LTable", [
	"table" => new LGuide(),
	"header" => [
		"id" => [
			"label" => "#",
			"style" => "min-width: 0px; width: 10px;"
		],
		"name" => [
			"label" => "Название справочника"
		]
	],
	"id" => "guide-table",
	"hideArrow" => "true",
	"controls" => [
		"table-edit" => "glyphicon glyphicon-pencil",
		"table-remove" => "glyphicon glyphicon-remove confirm"
	],
	"disablePagination" => "true"
]);