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

$table = $this->getWidget("LGridView", [
    "model" => new LDirection(),
    "id" => "direction-grid"
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