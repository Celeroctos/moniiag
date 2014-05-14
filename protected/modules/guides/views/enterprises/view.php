<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/enterprises.js"></script>
<script type="text/javascript">
    globalVariables.guideEdit = '<?php echo Yii::app()->user->checkAccess('guideEditEnterprise'); ?>';
</script>
<table id="enterprises"></table>
<div id="enterprisesPager"></div>
<div class="btn-group default-margin-top">
    <?php if(Yii::app()->user->checkAccess('guideAddEnterprise')) { ?>
    <button type="button" class="btn btn-default" id="addEnterprise">Добавить запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideEditEnterprise')) { ?>
    <button type="button" class="btn btn-default" id="editEnterprise">Редактировать выбранную запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideDeleteEnterprise')) { ?>
    <button type="button" class="btn btn-default" id="deleteEnterprise">Удалить запись</button>
    <?php } ?>
</div>
<div class="modal fade" id="addEnterprisePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить учреждение</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'shortName'),
                'id' => 'enterprise-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/enterprises/add'),
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
                                <?php echo $form->labelEx($model,'shortName', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->textField($model,'shortName', array(
                                        'id' => 'shortName',
                                        'class' => 'form-control',
                                        'placeholder' => 'Краткое название'
                                    )); ?>
                                    <?php echo $form->error($model,'shortName'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo $form->labelEx($model,'fullName', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->textField($model,'fullName', array(
                                        'id' => 'fullName',
                                        'class' => 'form-control',
                                        'placeholder' => 'Полное название'
                                    )); ?>
                                    <?php echo $form->error($model,'fullName'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo $form->labelEx($model,'addressFact', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->textField($model,'addressFact', array(
                                        'id' => 'addressFact',
                                        'class' => 'form-control',
                                        'placeholder' => 'Адрес фактический'
                                    )); ?>
                                    <?php echo $form->error($model,'addressFact'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo $form->labelEx($model,'addressJur', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->textField($model,'addressJur', array(
                                        'id' => 'addressJur',
                                        'class' => 'form-control',
                                        'placeholder' => 'Адрес юридический'
                                    )); ?>
                                    <?php echo $form->error($model,'addressJur'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo $form->labelEx($model,'phone', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->textField($model,'phone', array(
                                        'id' => 'phone',
                                        'class' => 'form-control',
                                        'placeholder' => 'Телефон'
                                    )); ?>
                                    <?php echo $form->error($model,'phone'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo $form->labelEx($model,'bank', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->textField($model,'bank', array(
                                        'id' => 'bank',
                                        'class' => 'form-control',
                                        'placeholder' => 'Банк'
                                    )); ?>
                                    <?php echo $form->error($model,'bank'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo $form->labelEx($model,'bankAccount', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->textField($model,'bankAccount', array(
                                        'id' => 'bankAccount',
                                        'class' => 'form-control',
                                        'placeholder' => 'Расчётный счёт'
                                    )); ?>
                                    <?php echo $form->error($model,'bankAccount'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo $form->labelEx($model,'inn', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->textField($model,'inn', array(
                                        'id' => 'inn',
                                        'class' => 'form-control',
                                        'placeholder' => 'ИНН'
                                    )); ?>
                                    <?php echo $form->error($model,'inn'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo $form->labelEx($model,'kpp', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->textField($model,'kpp', array(
                                        'id' => 'ogrn',
                                        'class' => 'form-control',
                                        'placeholder' => 'КПП'
                                    )); ?>
                                    <?php echo $form->error($model,'kpp'); ?>
                                </div>
                            </div>
							<div class="form-group">
                                <?php echo $form->labelEx($model,'ogrn', array(
                                	'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->textField($model,'ogrn', array(
                                    	'id' => 'ogrn',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'ОГРН'
                                    )); ?>
                                    <?php echo $form->error($model,'ogrn'); ?>
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
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <!--<button type="button" class="btn btn-primary">Добавить</button>-->
                <?php echo CHtml::ajaxSubmitButton(
                        'Добавить',
                        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/enterprises/add'),
                        array(
                            'success' => 'function(data, textStatus, jqXHR) {
                                $("#enterprise-add-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade" id="editEnterprisePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать учреждение</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'shortName'),
                'id' => 'enterprise-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/enterprises/edit'),
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
                            <?php echo $form->labelEx($model,'shortName', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'shortName', array(
                                    'id' => 'shortName',
                                    'class' => 'form-control',
                                    'placeholder' => 'Краткое название'
                                )); ?>
                                <?php echo $form->error($model,'shortName'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'fullName', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'fullName', array(
                                    'id' => 'fullName',
                                    'class' => 'form-control',
                                    'placeholder' => 'Полное название'
                                )); ?>
                                <?php echo $form->error($model,'fullName'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'addressFact', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'addressFact', array(
                                    'id' => 'addressFact',
                                    'class' => 'form-control',
                                    'placeholder' => 'Адрес фактический'
                                )); ?>
                                <?php echo $form->error($model,'addressFact'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'addressJur', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'addressJur', array(
                                    'id' => 'addressJur',
                                    'class' => 'form-control',
                                    'placeholder' => 'Адрес юридический'
                                )); ?>
                                <?php echo $form->error($model,'addressJur'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'phone', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'phone', array(
                                    'id' => 'phone',
                                    'class' => 'form-control',
                                    'placeholder' => 'Телефон'
                                )); ?>
                                <?php echo $form->error($model,'phone'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'bank', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'bank', array(
                                    'id' => 'bank',
                                    'class' => 'form-control',
                                    'placeholder' => 'Банк'
                                )); ?>
                                <?php echo $form->error($model,'bank'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'bankAccount', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'bankAccount', array(
                                    'id' => 'bankAccount',
                                    'class' => 'form-control',
                                    'placeholder' => 'Расчётный счёт'
                                )); ?>
                                <?php echo $form->error($model,'bankAccount'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'inn', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'inn', array(
                                    'id' => 'inn',
                                    'class' => 'form-control',
                                    'placeholder' => 'ИНН'
                                )); ?>
                                <?php echo $form->error($model,'inn'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'kpp', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'kpp', array(
                                    'id' => 'kpp',
                                    'class' => 'form-control',
                                    'placeholder' => 'КПП'
                                )); ?>
                                <?php echo $form->error($model,'kpp'); ?>
                            </div>
                        </div>
						<div class="form-group">
                            <?php echo $form->labelEx($model,'ogrn', array(
                            	'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'ogrn', array(
                                	'id' => 'ogrn',
                                	'class' => 'form-control',
                                	'placeholder' => 'ОГРН'
                                )); ?>
                                <?php echo $form->error($model,'kpp'); ?>
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
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/enterprises/edit'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#enterprise-edit-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade error-popup" id="errorAddEnterprisePopup">
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
