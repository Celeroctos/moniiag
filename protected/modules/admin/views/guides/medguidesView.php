<?php $this->widget('application.modules.admin.components.widgets.MedguidesTabMenu') ?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/medguides.js"></script>
<?php if($currentGuideId != -1) { ?>
    <script type="text/javascript">
        globalVariables.currentGuideId = <?php echo $currentGuideId; ?>;
    </script>
    <table id="medguides"></table>
    <div id="medguidesPager"></div>
    <div class="btn-group default-margin-top">
        <button type="button" class="btn btn-default" id="addMedGuide">Добавить запись</button>
        <button type="button" class="btn btn-default" id="editMedGuide">Редактировать выбранную запись</button>
        <button type="button" class="btn btn-default" id="deleteMedGuide">Удалить запись</button>
    </div>
    <div class="modal fade" id="addMedGuidePopup">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Добавить значение справочника</h4>
                </div>
                <?php
                $form = $this->beginWidget('CActiveForm', array(
                    'focus' => array($model,'name'),
                    'id' => 'medguide-add-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => true,
                    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/guides/addinguide?id='.$currentGuideId),
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
                        'Добавить',
                        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/guides/addinguide?id='.$currentGuideId),
                        array(
                            'success' => 'function(data, textStatus, jqXHR) {
                                    $("#medguide-add-form").trigger("success", [data, textStatus, jqXHR])
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
    <div class="modal fade" id="editMedGuidePopup">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Редактировать значение справочника</h4>
                </div>
                <?php
                $form = $this->beginWidget('CActiveForm', array(
                    'focus' => array($model,'name'),
                    'id' => 'medguide-edit-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => true,
                    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/guides/editinguide?id='.$currentGuideId),
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
            </div>
        </div>
    </div>
    <div class="modal fade error-popup" id="errorAddMedGuidePopup">
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
<?php } ?>