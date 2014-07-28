<script type="text/javascript" src="/assets/libs/jquery-json.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/chooser.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/insurances.js"></script>
<table id="insurances"></table>
<div id="insurancesPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addInsurance">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editInsurance">Редактировать запись</button>
    <button type="button" class="btn btn-default" id="deleteInsurance">Удалить запись</button>
</div>

<div class="modal fade" id="addInsurancePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить страховую компанию</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'insurance-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/insurances/add'),
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
                            <?php echo $form->hiddenField($model,'regionsHidden', array(
                                'id' => 'insuranceRegionsHiddenAdd',
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
                                <?php echo $form->error($model,'value'); ?>
                            </div>
                        </div>
                        <div class="form-group chooser" id="insuranceRegionsChooserAdd">
                            <label for="doctor" class="col-xs-3 control-label">Регион</label>

                            <div class="col-xs-9">
                                <input type="text" class="form-control" id="doctor"
                                       placeholder="Начинайте вводить...">
                                <ul class="variants no-display">
                                </ul>
                                <div class="choosed">

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/insurances/add'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                    $("#insurance-add-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade" id="editInsurancePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать страховую компанию</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'insurance-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/insurances/edit'),
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
                            <?php echo $form->hiddenField($model,'regionsHidden', array(
                                'id' => 'insuranceRegionsHiddenEdit',
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
                                <?php echo $form->error($model,'value'); ?>
                            </div>
                        </div>

                        <div class="form-group chooser" id="insuranceRegionsChooserEdit">
                            <label for="doctor" class="col-xs-3 control-label">Регион</label>

                            <div class="col-xs-9">
                                <input type="text" class="form-control" id="doctor"
                                       placeholder="Начинайте вводить...">
                                <ul class="variants no-display">
                                </ul>
                                <div class="choosed">

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
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/insurances/edit'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                    $("#insurance-edit-form").trigger("success", [data, textStatus, jqXHR])
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