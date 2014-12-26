<?php $form = $this->beginWidget('CActiveForm', array(
    'focus' => array($model,'name'),
    'id' => 'guide-add-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/guides/add'),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
)); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <?php echo $form->labelEx($model,'name', array(
                    'class' => 'col-xs-3 control-label'
                )); ?>
                <div class="col-xs-9">
                    <?php echo $form->textField($model,'name', array(
                        'id' => 'name',
                        'class' => 'form-control',
                        'placeholder' => 'Название категории'
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
        'Добавить',
        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/guides/add'),
        array(
            'success' => 'function(data, textStatus, jqXHR) {
                            $("#guide-add-form").trigger("success", [data, textStatus, jqXHR])
                        }'
        ),
        array(
            'class' => 'btn btn-primary'
        )
    ); ?>
</div>

<?php $this->endWidget(); ?>