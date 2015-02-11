<?php
/**
 * Шаблон Обновления
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
$this->pageTitle='Обновление медперсонала';
?>
<h3>Редактирование <?= '#'.$record->id ?></h3>
<?php
	$form=$this->beginWidget('CActiveForm', [
			'method'=>'post',
//			'enableAjaxValidation'=>true,
//			'enableClientValidation'=>true,
			'action'=>$this->createUrl('medworkers/update', ['id'=>$record->id]),
			'htmlOptions' => [
				'class' => 'form-horizontal col-xs-12',
				'role' => 'form'
			]
	]);
?>
<div class="row">
	<div class="col-xs-12">
		<?= $form->errorSummary($record, '', '', [
			'class'=>'alert alert-warning',
		]); ?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="form-group">
			<?= $form->Label($record, 'name', ['class'=>'col-xs-3 control-label']); ?>
			<div class="col-xs-6">
				<?= $form->TextField($record, 'name', [
								'class'=>'form-control',
							]); ?>
			</div>
		</div>
		<div class="form-group">
			<?= $form->Label($record, 'type', ['class'=>'col-xs-3 control-label']); ?>
			<div class="col-xs-6">
				<?= CHtml::activeDropDownList($record, 'type', $typeList, [
					'class'=>'form-control',
				]); ?>
			</div>
		</div>
		<div class="form-group">
			<?= $form->Label($record, 'payment_type', ['class'=>'col-xs-3 control-label']); ?>
			<div class="col-xs-6">
				<?= CHtml::activeDropDownList($record, 'payment_type', $payment_typeList, [
					'class'=>'form-control',
				]); ?>
			</div>
		</div>
		<div class="form-group">
			<?= $form->Label($record, 'is_medworker', ['class'=>'col-xs-3 control-label']); ?>
			<div class="col-xs-6">
				<?= CHtml::activeDropDownList($record, 'is_medworker', $is_medworkerList, [
					'class'=>'form-control',
				]); ?>
			</div>
		</div>
		<div class="form-group">
			<?= $form->Label($record, 'is_for_pregnants', ['class'=>'col-xs-3 control-label']); ?>
			<div class="col-xs-6">
				<?= CHtml::activeDropDownList($record, 'is_for_pregnants', $is_for_pregnantsList, [
					'class'=>'form-control',
				]); ?>
			</div>
		</div>
		<div class="form-group">
			<?= $form->Label($record, 'medcard_templates', ['class'=>'col-xs-3 control-label']); ?>
			<div class="col-xs-8">
				<?= CHtml::activeCheckBoxList($record, 'medcard_templates', Medpersonal::getMedcard_templatesList(), [
				]); ?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-7 col-xs-offset-3">
				<?= CHtml::submitButton('Редактировать', [
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