<?php $form = $this->beginWidget('CActiveForm', array(
    'focus' => array($model,'name'),
    'id' => 'template-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/templates/edit'),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
)); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <div class="form-group">
                    <?php echo $form->hiddenField($model,'id', array(
                        'id' => 'id',
                        'class' => 'form-control'
                    )); ?>
                    <?php echo $form->labelEx($model,'name', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'name', array(
                            'id' => 'name',
                            'class' => 'form-control',
                            'placeholder' => 'Название'
                        )); ?>
                        <?php echo $form->error($model,'name'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'categorieIds', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->dropDownList($model, 'categorieIds', $categoriesList, array(
                            'id' => 'categorieIds',
                            'class' => 'form-control',
                            'multiple' => 'multiple'
                        )); ?>
                        <?php echo $form->error($model,'categorieIds'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'pageId', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->dropDownList($model, 'pageId', $pagesList, array(
                            'id' => 'pageId',
                            'class' => 'form-control'
                        )); ?>
                        <?php echo $form->error($model,'pageId'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'primaryDiagnosisFilled', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->dropDownList($model, 'primaryDiagnosisFilled', array('Нет', 'Да'), array(
                            'id' => 'primaryDiagnosisFilled',
                            'class' => 'form-control'
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'index', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->numberField($model,'index', array(
                            'id' => 'index',
                            'class' => 'form-control',
                            'placeholder' => 'Порядковый номер для отображения'
                        )); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    <?php echo CHtml::ajaxSubmitButton(
        'Сохранить',
        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/templates/edit'),
        array(
            'success' => 'function(data, textStatus, jqXHR) {
                                $("#template-edit-form").trigger("success", [data, textStatus, jqXHR])
                            }'
        ),
        array(
            'class' => 'btn btn-primary'
        )
    ); ?>
</div>

<?php $this->endWidget(); ?>