<?php
/**
 * @var TreatmentController $this - Self instance
 */

$this->widget("LModal", [
	"title" => "Создать направление",
	"body" => $this->getWidget("LForm", [
		"model" => new LDirectionForm(),
		"id" => "direction-register-form",
		"url" => Yii::app()->getBaseUrl() . "/laboratory/direction/register"
	]),
	"id" => "direction-register-modal",
	"buttons" => [
		"register" => [
			"text" => "Создать",
			"class" => "btn btn-primary",
			"type" => "submit"
		]
	]
]);

$this->widget("LModal", [
	"title" => "Поиск медкарты",
	"body" => CHtml::tag("div", [
		"style" => "padding: 10px"
	], $this->getWidget("LMedcardSearch")),
	"id" => "medcard-search-modal",
	"buttons" => [
		"load" => [
			"text" => "Открыть",
			"class" => "btn btn-primary",
			"attributes" => [
				"data-loading-text" => "Загрузка ..."
			],
			"type" => "button"
		]
	],
	"class" => "modal-lg"
]);

$this->widget("LModal", [
	"title" => "Медицинская карта",
	"body" => $this->getWidget("LMedcardEditableViewer", [
		"number" => "0/12"
	]),
	"id" => "medcard-editable-viewer-modal",
	"class" => "modal-lg"
]);

?>
<div class="treatment-header-wrapper">
	<div align="center" class="col-xs-12 col-xs-offset-6 treatment-header">
		<div class="col-xs-12">
			<div class="treatment-header-rounded">
				<div class="row col-xs-12">
					<span class="col-xs-10">
						<b>Процедурный кабинет</b><br>
						<span><?= Yii::app()->user->getState("fio") ?></span>
					</span>
					<button class="btn btn-default col-xs-2 logout-button">Выйти</button>
				</div>
			</div>
		</div>
		<div class="col-xs-4">
			<button class="btn btn-default btn-block treatment-header-rounded active" type="button">
				<span>Направления</span>
			</button>
		</div>
		<div class="col-xs-4">
			<button class="btn btn-default btn-block treatment-header-rounded" type="button">
				<span>Повторный забор образцов</span>
				<span class="badge">3</span>
			</button>
		</div>
		<div class="col-xs-4">
			<button class="btn btn-default btn-block treatment-header-rounded" type="button" data-toggle="dropdown" aria-expanded="false">
				<span>Создать направление</span>
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a data-toggle="modal" data-target="#medcard-search-modal"">Для существующего пациента</a></li>
				<li><a data-toggle="modal" data-target="#medcard-editable-viewer-modal">Для нового пациента</a></li>
			</ul>
		</div>
	</div>
	<div class="col-xs-12 treatment-table-wrapper">
		<?= $this->getWidget("LGridView", [
			"model" => new LDirection(),
			"id" => "direction-grid"
		]) ?>
	</div>
</div>
