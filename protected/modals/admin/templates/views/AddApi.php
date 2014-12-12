<? $form = $this->beginWidget('CActiveForm', array(
    'focus' => array($model, 'description'),
    'id' => 'api-add-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/api/add'),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
)); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <?php echo $form->labelEx($model,'description', array(
                    'class' => 'col-xs-3 control-label'
                )); ?>
                <div class="col-xs-9">
                    <?php echo $form->textField($model,'description', array(
                        'id' => 'description',
                        'class' => 'form-control',
                        'placeholder' => 'Описание'
                    )); ?>
                    <?php echo $form->error($model, 'description'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    <?php echo CHtml::ajaxSubmitButton(
        'Добавить',
        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/api/add'),
        array(
            'success' => 'function(data, textStatus, jqXHR) {
                            $("#api-add-form").trigger("success", [data, textStatus, jqXHR])
                        }'
        ),
        array(
            'class' => 'btn btn-primary'
        )
    ); ?>
</div>

<? $this->endWidget(); ?>