<<<<<<< HEAD
<? $form = $this->beginWidget('CActiveForm', array(
=======
<?php $form = $this->beginWidget('CActiveForm', array(
>>>>>>> ad39ad672c8dcd2bd7088bb798980c66486cc6ca
    'focus' => array($model,'name'),
    'id' => 'categorie-edit-form',
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
                    <?php echo $form->error($model,'parentId'); ?>
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
        'Сохранить',
        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/categories/edit'),
        array(
            'success' => 'function(data, textStatus, jqXHR) {
                                $("#categorie-edit-form").trigger("success", [data, textStatus, jqXHR])
                            }'
        ),
        array(
            'class' => 'btn btn-primary'
        )
    ); ?>
</div>

<<<<<<< HEAD
<? $this->endWidget(); ?>
=======
<?php $this->endWidget(); ?>
>>>>>>> ad39ad672c8dcd2bd7088bb798980c66486cc6ca
