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

$table = $this->getWidget("zii.widgets.grid.CGridView", [
	"dataProvider" => LDirection::model()->getDataProvider(),
	"id" => "direction-grid",
	'itemsCssClass' => 'table table-bordered table-striped',
	'pager' => [
		'class' => 'CLinkPager',
		'selectedPageCssClass' => 'active',
		'header' => '',
		'htmlOptions' => [
			'class' => 'pagination',
		]
	],
	'htmlOptions' => array(
		'class' => 'container', // this is the class for the whole CGridView
	),
	'cssFile' => false, // Prevents Yii default CSS for CGridView
	"columns" => [
		"id" => [
			"name" => "#"
		],
		"surname" => [
			"name" => "Фамилия"
		],
		"name" => [
			"name" => "Имя"
		],
		"patronymic" => [
			"name" => "Отчество"
		],
		"card" => [
			"name" => "Номер карты"
		],
		"sender_id" => [
			"name" => "Направитель"
		],
		"analysis_type_id" => [
			"name" => "Тип анализа"
		]
	]
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
			<button class="btn btn-default btn-block treatment-header-rounded" type="button">
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
			<button class="btn btn-default btn-block treatment-header-rounded" type="button" data-toggle="modal" data-target="#direction-register-modal">
				<span>Создать направление</span>
			</button>
		</div>
	</div>
	<div class="col-xs-12 treatment-table-wrapper">
		<?= $table ?>
	</div>
</div>