<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/cabinets.js"></script>
<script type="text/javascript">
    globalVariables.guideEdit = '<?php echo Yii::app()->user->checkAccess('guideEditCabinet'); ?>';
</script>
<table id="cabinets"></table>
<div id="cabinetsPager"></div>
<div class="btn-group default-margin-top">
    <?php if(Yii::app()->user->checkAccess('guideAddCabinet')) { ?>
    <button type="button" class="btn btn-default" id="addCabinet">Добавить запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideEditCabinet')) { ?>
    <button type="button" class="btn btn-default" id="editCabinet">Редактировать выбранную запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideDeleteCabinet')) { ?>
    <button type="button" class="btn btn-default" id="deleteCabinet">Удалить выбранные</button>
    <?php } ?>
</div>
<div class="modal fade" id="addCabinetPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить персонал</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'description'),
                'id' => 'cabinet-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/cabinets/add'),
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
                            <?php echo $form->labelEx($model,'cabNumber', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'cabNumber', array(
                                    'id' => 'cabNumber',
                                    'class' => 'form-control',
                                    'placeholder' => 'Номер'
                                )); ?>
                                <?php echo $form->error($model,'cabNumber'); ?>
                            </div>
                        </div>
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
                                <?php echo $form->error($model,'description'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'enterpriseId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'enterpriseId', $enterprisesList, array(
                                    'id' => 'enterpriseId',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'enterpriseId'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'wardId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'wardId', $wardsList, array(
                                    'id' => 'wardId',
                                    'class' => 'form-control',
                                    'disabled' => 'true',
                                    'options' => array('-1' => array('selected' => true))
                                )); ?>
                                <?php echo $form->error($model,'wardId'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <!--<button type="button" class="btn btn-primary">Добавить</button>-->
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/cabinets/add'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#cabinet-add-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade" id="editCabinetPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать персонал</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'shortName'),
                'id' => 'cabinet-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/cabinets/edit'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <?php echo $form->hiddenField($model,'id', array(
                            'id' => 'id',
                            'class' => 'form-control'
                        )); ?>
                        <?php echo $form->hiddenField($model,'enterpriseId', array(
                            'id' => 'enterpriseId',
                            'class' => 'form-control'
                        )); ?>
                        <?php echo $form->hiddenField($model,'wardId', array(
                            'id' => 'wardId',
                            'class' => 'form-control'
                        )); ?>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'cabNumber', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'cabNumber', array(
                                    'id' => 'cabNumber',
                                    'class' => 'form-control',
                                    'placeholder' => 'Номер'
                                )); ?>
                                <?php echo $form->error($model,'cabNumber'); ?>
                            </div>
                        </div>
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
                                <?php echo $form->error($model,'description'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Редактировать',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/cabinets/edit'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#cabinet-edit-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade error-popup" id="errorAddCabinetPopup">
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
