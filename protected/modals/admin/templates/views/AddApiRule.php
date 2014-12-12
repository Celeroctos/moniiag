<? $form = $this->beginWidget('CActiveForm', array(
    'focus' => array($model, 'description'),
    'id' => 'rule-add-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/apirule/add'),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
)); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-xs-12">
			<div class="form-group">
				<?php echo $form->labelEx($model,'api_key', array(
					'class' => 'col-xs-3 control-label'
				)); ?>
				<div class="col-xs-9">
					<?php echo $form->dropDownList($model,'api_key', $apiList, array(
						'id' => 'apiKey',
						'class' => 'form-control',
						'placeholder' => 'Ключ API'
					)); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model, 'controller', array(
					'class' => 'col-xs-3 control-label'
				)); ?>
				<div class="col-xs-9">
					<?php echo $form->textField($model,'controller', array(
						'id' => 'controller',
						'class' => 'form-control',
						'placeholder' => 'Путь к контроллеру'
					)); ?>
					<?php echo $form->error($model, 'controller'); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'writable', array(
					'class' => 'col-xs-3 control-label'
				)); ?>
				<div class="col-xs-9">
					<?php echo $form->dropDownList($model,'writable', array('Нет', 'Да'), array(
						'id' => 'writable',
						'class' => 'form-control'
					)); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model, 'readable', array(
					'class' => 'col-xs-3 control-label'
				)); ?>
				<div class="col-xs-9">
					<?php echo $form->dropDownList($model, 'readable', array('Нет', 'Да'), array(
						'id' => 'readable',
						'class' => 'form-control'
					)); ?>
				</div>
			</div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    <?php echo CHtml::ajaxSubmitButton(
        'Добавить',
        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/apirule/add'),
        array(
            'success' => 'function(data, textStatus, jqXHR) {
                            $("#rule-add-form").trigger("success", [data, textStatus, jqXHR])
                        }'
        ),
        array(
            'class' => 'btn btn-primary'
        )
    ); ?>
</div>

<? $this->endWidget(); ?>