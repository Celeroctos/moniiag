<?php
/**
 * Шаблон обновления отделения
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
$this->pageTitle='Обновление отделений';
?>
<h3>Редактирование</h3>
<?php if(Yii::app()->user->hasFlash('success')): ?>
	<div class="alert alert-success">
		<?= Yii::app()->user->getFlash('success'); ?>
	</div>
<?php endif; ?>
<?php
	$form=$this->beginWidget('CActiveForm', [
			'method'=>'post',
//			'enableAjaxValidation'=>true,
//			'enableClientValidation'=>true,
			'action'=>$this->createUrl('wards/update', ['id'=>$record->id]),
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
			<?= $form->Label($record, 'name', ['class'=>'col-xs-2 control-label']); ?>
			<div class="col-xs-6">
				<?= $form->TextField($record, 'name', [
								'class'=>'form-control',
							]); ?>
			</div>
		</div>
		<div class="form-group">
			<?= $form->Label($record, 'enterprise_id', ['class'=>'col-xs-2 control-label']); ?>
			<div class="col-xs-6">
				<?= CHtml::activeDropDownList($record, 'enterprise_id', $enterpriseList, [
					'class'=>'form-control',
				]); ?>
			</div>
		</div>
		<div class="form-group">
			<?= $form->Label($record, 'rule_id', ['class'=>'col-xs-2 control-label']); ?>
			<div class="col-xs-6">
				<?= CHtml::activeDropDownList($record, 'rule_id', $medcardruleList, [
					'class'=>'form-control',
				]); ?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-7 col-xs-offset-2">
				<?= CHtml::submitButton('Редактировать', [
					'class'=>'btn btn-primary'
				]); ?>
				<?= CHtml::link('Вернуться назад', $this->createUrl('wards/view'), [
					'class'=>'btn btn-default'
				]); ?>
			</div>
		</div>
	</div>
</div>
<?php $this->endWidget(); ?>