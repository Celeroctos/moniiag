<?php $form = $this->beginWidget('CActiveForm', array(
    'focus' => array($model,'name'),
    'id' => 'medguide-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/guides/editinguide?id='.$currentGuideId),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
)); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <?php echo $form->hiddenField($model,'id', array(
                    'id' => 'id',
                    'class' => 'form-control'
                )); ?>
                <?php echo $form->labelEx($model,'value', array(
                    'class' => 'col-xs-3 control-label'
                )); ?>
                <div class="col-xs-9">
                    <?php echo $form->textField($model,'value', array(
                        'id' => 'value',
                        'class' => 'form-control',
                        'placeholder' => 'Значение'
                    )); ?>
                    <?php echo $form->error($model,'value'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    <?php echo CHtml::ajaxSubmitButton(
        'Сохранить',
        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/guides/editinguide?id='.$currentGuideId),
        array(
            'success' => 'function(data, textStatus, jqXHR) {
                                    $("#medguide-edit-form").trigger("success", [data, textStatus, jqXHR])
                                }'
        ),
        array(
            'class' => 'btn btn-primary'
        )
    ); ?>
</div>
<?php $this->endWidget(); ?>
