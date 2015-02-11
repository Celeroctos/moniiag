<?php
/**
 * Шаблон создания медперсонала
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
$this->pageTitle='Создание медперсонала';
?>
<h3>Добавление</h3>
<?php
	$form=$this->beginWidget('CActiveForm', [
			'method'=>'post',
//			'enableAjaxValidation'=>true,
//			'enableClientValidation'=>true,
			'action'=>$this->createUrl('medworkers/create'),
			'htmlOptions' => [
				'class' => 'form-horizontal col-xs-12',
				'role' => 'form'
			]
	]);
?>
<div class="row">
	<div class="col-xs-12">
		<?= $form->errorSummary($model, '', '', [
			'class'=>'alert alert-warning',
		]); ?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="form-group">
			<?= $form->Label($model, 'name', ['class'=>'col-xs-2 control-label']); ?>
			<div class="col-xs-6">
				<?= $form->TextField($model, 'name', [
								'class'=>'form-control',
							]); ?>
			</div>
		</div>
		<div class="form-group">
			<?= $form->Label($model, 'payment_type', ['class'=>'col-xs-2 control-label']); ?>
			<div class="col-xs-6">
				<?= CHtml::activeDropDownList($model, 'payment_type', $payment_typeList, [
					'class'=>'form-control',
				]); ?>
			</div>
		</div>
		<div class="form-group">
			<?= $form->Label($model, 'is_medworker', ['class'=>'col-xs-2 control-label']); ?>
			<div class="col-xs-6">
				<?= CHtml::activeDropDownList($model, 'is_medworker', $is_medworkerList, [
					'class'=>'form-control',
				]); ?>
			</div>
		</div>
		<div class="form-group">
			<?= $form->Label($model, 'is_for_pregnants', ['class'=>'col-xs-2 control-label']); ?>
			<div class="col-xs-6">
				<?= CHtml::activeDropDownList($model, 'is_for_pregnants', $is_for_pregnantsList, [
					'class'=>'form-control',
				]); ?>
			</div>
		</div>
		<div class="form-group">
			<?= $form->Label($model, 'medcard_templates', ['class'=>'col-xs-2 control-label']); ?>
			<div class="col-xs-7">
				<?= CHtml::activeCheckBoxList($model, 'medcard_templates', Medpersonal::getMedcard_templatesList(), [
					''=>'checked',
				]); ?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-7 col-xs-offset-2">
				<?= CHtml::submitButton('Добавить', [
					'class'=>'btn btn-primary'
				]); ?>
				<?= CHtml::link('Вернуться назад', $this->createUrl('medworkers/view'), [
					'class'=>'btn btn-default'
				]); ?>
			</div>
		</div>
	</div>
</div>
<?php $this->endWidget(); ?>
