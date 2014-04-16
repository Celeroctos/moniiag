<h4>Краткая справка: Клинические диагнозы</h4>
<p>Раздел предназначен для редактирования клинических диагнозов для медкарты. 
</p>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/clinicalDiagnosis.js"></script>
<table id="diagnosiss"></table>
<div id="diagnosissPager"></div>
<div id="medguidesPager"></div>
<div class="btn-group default-margin-top">
        <button type="button" class="btn btn-default" id="addClinicalDiagnosis">Добавить запись</button>
        <button type="button" class="btn btn-default" id="editClinicalDiagnosis">Редактировать выбранную запись</button>
        <button type="button" class="btn btn-default" id="deleteClinicalDiagnosis">Удалить выбранные</button>
</div>




 <div class="modal fade" id="addClinicalDiagnosisPopup">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Добавить клинический диагноз</h4>
                </div>
                <?php
                $form = $this->beginWidget('CActiveForm', array(
                	'focus' => array($model,'name'),
                	'id' => 'clinic-add-form',
                	'enableAjaxValidation' => true,
                	'enableClientValidation' => true,
                	'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/diagnosis/addclinic'),
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
                                <?php echo $form->labelEx($model,'description', array(
                                	'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->textField($model,'description', array(
                                    	'id' => 'description',
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
                    	CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/diagnosis/addclinic'),
                    	array(
                    			'success' => 'function(data, textStatus, jqXHR) {
                                    $("#clinic-add-form").trigger("success", [data, textStatus, jqXHR])
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
    <div class="modal fade" id="editClinicalDiagnosisPopup">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Редактировать клинический диагноз</h4>
                </div>
                <?php
                $form = $this->beginWidget('CActiveForm', array(
                	'focus' => array($model,'name'),
                	'id' => 'clinic-edit-form',
                	'enableAjaxValidation' => true,
                	'enableClientValidation' => true,
                	'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/diagnosis/editclinic'),
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
                                <?php echo $form->labelEx($model,'description', array(
                                'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->textField($model,'description', array(
                                    	'id' => 'description',
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
                    	'Отредактировать',
                    	CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/diagnosis/editclinic'),
                    	array(
                    			'success' => 'function(data, textStatus, jqXHR) {
                                    $("#clinic-edit-form").trigger("success", [data, textStatus, jqXHR])
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
    <div class="modal fade error-popup" id="errorPopup">
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