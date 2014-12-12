<? $form = $this->beginWidget('CActiveForm', array(
    'focus' => array($model, 'name'),
    'id' => 'api-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/api/edit'),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
)); ?>

    <div class="modal-body">
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group">
					<?php echo $form->hiddenField($model, 'key', array(
						'id' => 'key',
						'class' => 'form-control'
					)); ?>
                    <?php echo $form->labelEx($model, 'description', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model, 'description', array(
                            'id' => 'description',
                            'class' => 'form-control',
                            'placeholder' => 'Описание'
                        )); ?>
                        <?php echo $form->error($model,'name'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <?php echo CHtml::ajaxSubmitButton(
            'Сохранить',
            CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/api/edit'),
            array(
                'success' => 'function(data, textStatus, jqXHR) {
                            $("#api-edit-form").trigger("success", [data, textStatus, jqXHR])
                        }'
            ),
            array(
                'class' => 'btn btn-primary'
            )
        ); ?>
    </div>

<? $this->endWidget(); ?>