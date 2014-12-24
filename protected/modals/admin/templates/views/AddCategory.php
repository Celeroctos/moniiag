<?php $form = $this->beginWidget('CActiveForm', array(
    'focus' => array($model,'name'),
    'id' => 'categorie-add-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/categories/add'),
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
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'isDynamic', array(
                    'class' => 'col-xs-3 control-label'
                )); ?>
                <div class="col-xs-9">
                    <?php echo $form->dropDownList($model,'isDynamic', array('Нет', 'Да'), array(
                        'id' => 'isDynamic',
                        'class' => 'form-control'
                    )); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'position', array(
                    'class' => 'col-xs-3 control-label'
                )); ?>
                <div class="col-xs-9">
                    <?php echo $form->textField($model,'position', array(
                        'id' => 'position',
                        'class' => 'form-control'
                    )); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'isWrapped', array(
                    'class' => 'col-xs-3 control-label'
                )); ?>
                <div class="col-xs-9">
                    <?php echo $form->dropDownList($model,'isWrapped', array('Нет', 'Да'), array(
                        'id' => 'isWrapped',
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
        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/categories/add'),
        array(
            'success' => 'function(data, textStatus, jqXHR) {
                                $("#categorie-add-form").trigger("success", [data, textStatus, jqXHR])
                            }'
        ),
        array(
            'class' => 'btn btn-primary'
        )
    ); ?>
</div>

<?php $this->endWidget(); ?>