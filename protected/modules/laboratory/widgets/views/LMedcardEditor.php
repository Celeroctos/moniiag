<?php
/**
 * @var LMedcardEditor $this - Self instance
 * @var LModel $model - Medcard model
 * @var CActiveForm $form - Active form widget
 * @var array $privileges - List with privileges
 */

$form = $this->beginWidget('CActiveForm', [
	'id' => 'patient-medcard-edit-form',
	'htmlOptions' => [
		'class' => 'form-horizontal col-xs-12',
		'role' => 'form'
	],
	'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/reception/patient/editcard')
]);

$form->hiddenField($model, 'cardNumber', [
	'id' => 'cardNumber',
	'class' => 'form-control'
]);

$this->widget('application.modules.reception.components.widgets.MedcardFormWidget', [
	'form' => $form,
	'model' => $model,
	'privilegesList' => $privileges,
	'showEditIcon' => 1,
	'template' => 'application.modules.reception.components.widgets.views.MedcardFormWidget'
]);

$this->widget('application.modules.reception.components.widgets.MedcardFormWidget', [
    'form' => $form,
    'model' => $model,
    'privilegesList' => $privileges,
    'showEditIcon' => 1,
    'template' => 'application.modules.reception.components.widgets.views.addressEditPopup'
]);

$this->endWidget();