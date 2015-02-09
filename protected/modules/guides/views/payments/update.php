<?php
/**
 * Шаблон обновления типов оплат
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
$this->pageTitle='Обновление типов оплат';
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
			'action'=>$this->createUrl('payments/update', ['id'=>$record->id]),
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
			<?= $form->Label($record, 'name', ['class'=>'col-xs-1 control-label']); ?>
			<div class="col-xs-6">
				<?= $form->TextField($record, 'name', [
								'class'=>'form-control',
							]); ?>
			</div>
		</div>
		<div class="form-group">
			<?= $form->Label($record, 'tasu_string', ['class'=>'col-xs-1 control-label']); ?>
			<div class="col-xs-6">
				<?= $form->TextField($record, 'tasu_string', [
								'class'=>'form-control',
							]); ?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-6 col-xs-offset-1">
				<?= CHtml::submitButton('Редактировать', [
					'class'=>'btn btn-primary'
				]); ?>
				<?= CHtml::link('Вернуться назад', $this->createUrl('payments/view'), [
					'class'=>'btn btn-default'
				]); ?>
			</div>
		</div>
	</div>
</div>
<?php $this->endWidget(); ?>