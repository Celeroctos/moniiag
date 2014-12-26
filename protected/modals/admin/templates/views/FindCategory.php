<? $form = $this->beginWidget('CActiveForm', array(
	'focus' => array($model,'name'),
	'id' => 'guide-edit-form',
	'enableAjaxValidation' => true,
	'enableClientValidation' => true,
	'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/categories/edit'),
	'htmlOptions' => array(
		'class' => 'form-horizontal col-xs-12',
		'role' => 'form'
	)
)); ?>

<div class="modal-body">
	<div class="row">
		<div class="col-xs-12">
			<div class="form-group">
				<?php echo $form->labelEx($model,'parentId', array(
					'class' => 'col-xs-3 control-label'
				)); ?>
				<div class="col-xs-9">
					<?php echo $form->dropDownList($model,'parentId', $categoriesList, array(
						'id' => 'parentId',
						'class' => 'form-control',
						'placeholder' => 'Категория-родитель'
					)); ?>
					<?php echo $form->error($model,'parentId'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<img src="/moniiag/images/ajax-loader.gif" width="30" class="saving-template" style="margin-right: 20px">
	<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
	<button type="button" class="btn btn-warning">Перенести</button>
	<button type="button" class="btn btn-success">Клонировать</button>
</div>

<? $this->endWidget(); ?>