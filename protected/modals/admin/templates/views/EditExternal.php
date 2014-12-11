<? $form = $this->beginWidget('CActiveForm', array(
    'focus' => array($model, 'name'),
    'id' => 'external-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/external/edit'),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
)); ?>

    <div class="modal-body">
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group">
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
                <div class="form-group" style="visibility: hidden">
                    <?php echo $form->labelEx($model, 'key', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model, 'key', array(
                            'id' => 'key',
                            'class' => 'form-control',
                            'placeholder' => 'Ключ'
                        )); ?>
                        <?php echo $form->error($model, 'name'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <?php echo CHtml::ajaxSubmitButton(
            'Сохранить',
            CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/external/edit'),
            array(
                'success' => 'function(data, textStatus, jqXHR) {
                            $("#external-edit-form").trigger("success", [data, textStatus, jqXHR])
                        }'
            ),
            array(
                'class' => 'btn btn-primary'
            )
        ); ?>
    </div>

<? $this->endWidget(); ?>