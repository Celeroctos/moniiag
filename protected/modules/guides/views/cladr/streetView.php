<h4>КЛАДР</h4>
<p>Раздел предлагает инструменты управления Классификатором Адресов России.</p>
<?php $this->widget('application.modules.guides.components.widgets.CladrTabMenu', array(
));
?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/cladrstreets.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/chooser.js"></script>
<script type="text/javascript">
    globalVariables.guideEdit = '<?php echo Yii::app()->user->checkAccess('guideEditPrivelege'); ?>';
</script>
<table id="streets"></table>
<div id="streetsPager"></div>
<div class="btn-group default-margin-top">
    <?php if(Yii::app()->user->checkAccess('guideAddPrivelege')) { ?>
        <button type="button" class="btn btn-default" id="addStreet">Добавить запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideEditPrivelege')) { ?>
        <button type="button" class="btn btn-default" id="editStreet">Редактировать выбранную запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideDeletePrivelege')) { ?>
        <button type="button" class="btn btn-default" id="deleteStreet">Удалить выбранные</button>
    <?php } ?>
</div>
<div class="modal fade" id="addStreetPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить улицу</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'description'),
                'id' => 'street-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/cladr/streetadd'),
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
                                    'placeholder' => 'Название района'
                                )); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'codeCladr', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'codeCladr', array(
                                    'id' => 'codeCladr',
                                    'class' => 'form-control',
                                    'placeholder' => 'Код'
                                )); ?>
                            </div>
                        </div>
                        <div class="form-group chooser" id="regionChooser">
                            <?php echo $form->labelEx($model,'codeRegion', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'codeRegion', array(
                                    'id' => 'codeRegion',
                                    'class' => 'form-control',
                                    'placeholder' => 'Регион'
                                )); ?>
                                <ul class="variants no-display">
                                </ul>
                                <div class="choosed">
                                </div>
                            </div>
                        </div>
                        <div class="form-group chooser" id="districtChooser">
                            <?php echo $form->labelEx($model,'codeDistrict', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'codeDistrict', array(
                                    'id' => 'codeDistrict',
                                    'class' => 'form-control',
                                    'placeholder' => 'Район',
                                    'disabled' => true
                                )); ?>
                                <ul class="variants no-display">
                                </ul>
                                <div class="choosed">
                                </div>
                            </div>
                        </div>
                        <div class="form-group chooser" id="settlementChooser">
                            <?php echo $form->labelEx($model,'codeSettlement', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'codeSettlement', array(
                                    'id' => 'codeSettlement',
                                    'class' => 'form-control',
                                    'placeholder' => 'Населённый пункт',
                                    'disabled' => true
                                )); ?>
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
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/cladr/streetadd'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#street-add-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade" id="editStreetPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать улицу</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'shortName'),
                'id' => 'street-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/cladr/streetedit'),
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
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'name', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'name', array(
                                    'id' => 'name',
                                    'class' => 'form-control',
                                    'placeholder' => 'Название района'
                                )); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'codeCladr', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'codeCladr', array(
                                    'id' => 'codeCladr',
                                    'class' => 'form-control',
                                    'placeholder' => 'Код'
                                )); ?>
                            </div>
                        </div>
                        <div class="form-group chooser" id="regionChooser">
                            <?php echo $form->labelEx($model,'codeRegion', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'codeRegion', array(
                                    'id' => 'codeRegion',
                                    'class' => 'form-control',
                                    'placeholder' => 'Регион'
                                )); ?>
                                <ul class="variants no-display">
                                </ul>
                                <div class="choosed">
                                </div>
                            </div>
                        </div>
                        <div class="form-group chooser" id="districtChooser">
                            <?php echo $form->labelEx($model,'codeDistrict', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'codeDistrict', array(
                                    'id' => 'codeDistrict',
                                    'class' => 'form-control',
                                    'placeholder' => 'Район',
                                    'disabled' => true
                                )); ?>
                                <ul class="variants no-display">
                                </ul>
                                <div class="choosed">
                                </div>
                            </div>
                        </div>
                        <div class="form-group chooser" id="settlementChooser">
                            <?php echo $form->labelEx($model,'codeSettlement', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'codeSettlement', array(
                                    'id' => 'codeSettlement',
                                    'class' => 'form-control',
                                    'placeholder' => 'Населённый пункт',
                                    'disabled' => true
                                )); ?>
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
                    'Редактировать',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/cladr/streetedit'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#street-edit-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade error-popup" id="errorAddStreetPopup">
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