<?php
/**
 * Шаблон создания отделения
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
$this->pageTitle='Создание отделения';
?>
<h3>Добавление</h3>
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
			'action'=>$this->createUrl('wards/create'),
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
			<?= $form->Label($model, 'enterprise_id', ['class'=>'col-xs-2 control-label']); ?>
			<div class="col-xs-6">
				<?= CHtml::activeDropDownList($model, 'enterprise_id', $enterpriseList, [
					'class'=>'form-control',
				]); ?>
			</div>
		</div>
		<div class="form-group">
			<?= $form->Label($model, 'rule_id', ['class'=>'col-xs-2 control-label']); ?>
			<div class="col-xs-6">
				<?= CHtml::activeDropDownList($model, 'rule_id', $medcardruleList, [
					'class'=>'form-control',
				]); ?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-7 col-xs-offset-2">
				<?= CHtml::submitButton('Добавить', [
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