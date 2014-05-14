<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/medworkers.js"></script>
<script type="text/javascript">
    globalVariables.guideEdit = '<?php echo Yii::app()->user->checkAccess('guideEditMedworker'); ?>';
</script>
<table id="medworkers"></table>
<div id="medworkersPager"></div>
<div class="btn-group default-margin-top">
    <?php if(Yii::app()->user->checkAccess('guideAddMedworker')) { ?>
    <button type="button" class="btn btn-default" id="addMedworker">Добавить запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideEditMedworker')) { ?>
    <button type="button" class="btn btn-default" id="editMedworker">Редактировать выбранную запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideDeleteMedworker')) { ?>
    <button type="button" class="btn btn-default" id="deleteMedworker">Удалить запись</button>
    <?php } ?>
</div>
<div class="modal fade" id="addMedworkerPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить медработника</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'medworker-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/medworkers/add'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
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
                                    'placeholder' => 'Наименование'
                                )); ?>
                                <?php echo $form->error($model,'name'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'type', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'type', $typesList, array(
                                    'id' => 'type',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'type'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'isForPregnants', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'isForPregnants', array('Нет', 'Да'), array(
                                    'id' => 'isForPregnants',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'isForPregnants'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'paymentType', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'paymentType', array('ОМС', 'Бюджет'), array(
                                    'id' => 'paymentType',
                                    'class' => 'form-control'
                                )); ?>
                            </div>
                        </div>
                        <h5><strong>Шаблоны приёма:</strong></h5>
                        <div class="form-group">
                        <?php foreach ($allTemplates as $key => $template) { ?>
                            <div>
                            <input type="checkbox" value="<?php echo $template['id']; ?>" name="template<?php echo $template['id']; ?>">
                            <label class="control-label"><?php echo $template['name']; ?></label>
                            </div>
                        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/medworkers/add'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#medworker-add-form").trigger("success", [data, textStatus, jqXHR])
                            }'
                    ),
                    array(
                        'class' => 'btn btn-primary'
                    )
                ); ?>
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>
<div class="modal fade" id="editMedworkerPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать медработника</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'medworker-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/medworkers/edit'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
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
                                    'placeholder' => 'Наименование'
                                )); ?>
                                <?php echo $form->error($model,'name'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'type', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'type', $typesList, array(
                                    'id' => 'type',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'type'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'isForPregnants', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'isForPregnants', array('Нет', 'Да'), array(
                                    'id' => 'isForPregnants',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'isForPregnants'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'paymentType', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'paymentType', array('ОМС', 'Бюджет'), array(
                                    'id' => 'paymentType',
                                    'class' => 'form-control'
                                )); ?>
                            </div>
                        </div>
                        <h5><strong>Шаблоны приёма:</strong></h5>
                        <div class="form-group">
                        <?php foreach ($allTemplates as $key => $template) { ?>
                            <div>
                            <input type="checkbox" value="<?php echo $template['id']; ?>" name="template<?php echo $template['id']; ?>">
                            <label class="control-label"><?php echo $template['name']; ?></label>
                            </div>
                        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/medworkers/edit'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#medworker-edit-form").trigger("success", [data, textStatus, jqXHR])
                            }'
                    ),
                    array(
                        'class' => 'btn btn-primary'
                    )
                ); ?>
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="errorAddInsurancePopup">
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
