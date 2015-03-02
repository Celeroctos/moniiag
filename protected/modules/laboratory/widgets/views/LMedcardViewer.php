<?php
/**
 * @var LMedcardViewer $this - Widget instance
 * @var mixed $model - Medcard cortege
 */

CHtml::openTag("div", [
	"class" => "medcard-viewer"
]);

$this->beginWidget("LPanel", [
	"title" => "Реквизитная информация",
	"collapse" => "true"
]);

?>

<span class="medcard-info">
	<b>ФИО:&nbsp;</b> <?= $model["first_name"]." ".$model["middle_name"]." ".$model["last_name"] ?>&nbsp;
	<b>Возраст:&nbsp;</b> <?= $model["age"] ?>&nbsp;
	<b>Номер абмулаторной карты</b> <?= $model["card_number"] ?>
	<br>
	<b>Адрес:&nbsp;</b> <?= $model["address_str"] ?>
	<br>
	<b>Телефон:&nbsp;</b> <?= $model["contact"] ?>
	<br>
	<b>Место работы:&nbsp;</b> <?= $model["work_place"] ?>
</span>

<?

$this->endWidget();

$this->beginWidget("LPanel", [
	"title" => "Результаты обследования",
	"collapse" => "true"
]);
print "Не реализовано";
$this->endWidget();

$this->beginWidget("LPanel", [
	"title" => "Согласия пациента",
	"collapse" => "true"
]);
print "Не реализовано";
$this->endWidget();

$this->beginWidget("LPanel", [
	"title" => "История приемов",
	"collapse" => "true"
]);
print "Не реализовано";
$this->endWidget();

$this->beginWidget("LPanel", [
	"title" => "Печать документов",
	"collapse" => "true"
]);
print "Не реализовано";
$this->endWidget();

$this->beginWidget("LPanel", [
	"title" => "Направления",
	"collapse" => "true"
]);
print "Не реализовано";
$this->endWidget();

//print "<pre>";
//print_r($model);
//print "</pre>";

CHtml::closeTag("div");