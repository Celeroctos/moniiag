<?php

/**
 * @var LMedcardEditableViewer $this - Self widget instance
 */

$this->beginWidget("CActiveForm", [
	"id" => "medcard-editable-viewer-form",
	"enableClientValidation" => true,
	"enableAjaxValidation" => true,
	"action" => Yii::app()->getBaseUrl() . "/laboratory/medcard/register",
	"htmlOptions" => [
		"class" => "form-horizontal col-xs-12",
		"role" => 'form'
	]
]); ?>

<div class="row">
	<div class="col-xs-12 text-center medcard-editable-viewer-number">
		<b>Медицинская карта №&nbsp;<span id="card_number"></span><b>
	</div>
	<div class="col-xs-12">
		<div class="col-xs-6 text-center">
			<b>ОМС</b>
			<hr>
			<? $this->widget("LForm", [
				"model" => new LPolicyForm("treatment.policy")
			]) ?>
		</div>
		<div class="col-xs-6 text-center">
			<b>Сведения о работе</b>
			<hr>
			<? $this->widget("LForm", [
				"model" => new LMedcardForm2("treatment.show")
			]) ?>
		</div>
	</div>
</div>
<br>
<div class="row">
	<div class="col-xs-12 text-center">
		<span><b>Личные данные<b></span>
		<hr>
		<div class="col-xs-11 text-center">
			<? $this->widget("LForm", [
				"model" => new LPolicyForm("treatment.patient")
			]) ?>
		</div>
	</div>
</div>

<? $this->endWidget() ?>