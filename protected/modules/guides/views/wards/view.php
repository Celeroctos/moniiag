<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/wards.js"></script>
<table id="wards">
</table>
<div id="wardsPager">
</div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addWard">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editWard">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default" id="deleteWard">Удалить выбранные</button>
</div>
<div class="modal fade" id="addWardPopup">
    <div class="modal-dialog">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'focus' => array($model,'name'),
            'id' => 'ward-add-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/wards/add'),
            'htmlOptions' => array(
                'class' => 'form-horizontal col-xs-12',
                'role' => 'form'
            )
        ));
        ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Добавить отделение</h4>
                </div>
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
                                        'placeholder' => 'Название отделения'
                                    )); ?>
                                    <?php echo $form->error($model,'name'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo $form->labelEx($model,'enterprise', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->dropDownList($model, 'enterprise', $typesList, array(
                                        'id' => 'enterprise',
                                        'class' => 'form-control'
                                    )); ?>
                                    <?php echo $form->error($model,'enterprise'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <?php echo CHtml::ajaxSubmitButton(
                        'Добавить',
                        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/wards/add'),
                        array(
                            'success' => 'function(data, textStatus, jqXHR) {
                                $("#ward-add-form").trigger("success", [data, textStatus, jqXHR])
                            }'
                        ),
                        array(
                            'class' => 'btn btn-primary'
                        )
                    ); ?>
                </div>
            </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<div class="modal fade error-popup" id="errorAddWardPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Ошибка!</h4>
            </div>
            <div class="modal-body">
                <h4>При заполнении формы возникли следующие ошибки:</h4>
                <div class="row">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
