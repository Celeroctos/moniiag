<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/767e5633/jquery.yiiactiveform.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/searchAddPatient.js" ></script>
<h4>Редактирование карты пациента</h4>
<p class="text-left">
    Заполните поля формы, чтобы отредактировать карту существующего пациента <span class="text-danger bold">(<?php echo $fio; ?>, полис №<?php echo $policy_number; ?>, карта №<?php echo $card_number; ?>)</span>
</p>
<div class="row default-padding">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'patient-medcard-edit-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/editcard'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
        <div class="row">
            <div class="col-xs-6">
                <?php echo $form->hiddenField($model,'cardNumber', array(
                    'id' => 'cardNumber',
                    'class' => 'form-control'
                )); ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'doctype', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-5">
                        <?php echo $form->dropDownList($model, 'doctype', array(1 => 'Паспорт'), array(
                            'id' => 'doctype',
                            'class' => 'form-control'
                        )); ?>
                        <?php echo $form->error($model,'doctype'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'serie', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-5">
                        <?php echo $form->textField($model,'serie', array(
                            'id' => 'serie',
                            'class' => 'form-control',
                            'placeholder' => 'Серия'
                        )); ?>
                        <?php echo $form->error($model,'serie'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'docnumber', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-6">
                        <?php echo $form->textField($model,'docnumber', array(
                            'id' => 'docnumber',
                            'class' => 'form-control',
                            'placeholder' => 'Номер'
                        )); ?>
                        <?php echo $form->error($model,'docnumber'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'whoGived', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-8">
                        <?php echo $form->textField($model,'whoGived', array(
                            'id' => 'whoGived',
                            'class' => 'form-control',
                            'placeholder' => 'Кем выдан'
                        )); ?>
                        <?php echo $form->error($model,'whoGived'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'documentGivedate', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-6 input-group date" id="document-givedate-cont">
                        <?php echo $form->textField($model,'documentGivedate', array(
                            'id' => 'documentGivedate',
                            'class' => 'form-control',
                            'placeholder' => 'Дата выдачи'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'addressReg', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'addressReg', array(
                            'id' => 'addressReg',
                            'class' => 'form-control',
                            'placeholder' => 'Адрес регистрации'
                        )); ?>
                        <?php echo $form->error($model,'addressReg'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'address', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'address', array(
                            'id' => 'address',
                            'class' => 'form-control',
                            'placeholder' => 'Адрес проживания'
                        )); ?>
                        <?php echo $form->error($model,'address'); ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <?php echo $form->labelEx($model,'workPlace', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'workPlace', array(
                            'id' => 'workPlace',
                            'class' => 'form-control',
                            'placeholder' => 'Место работы'
                        )); ?>
                        <?php echo $form->error($model,'workPlace'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'workAddress', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'workAddress', array(
                            'id' => 'workAddress',
                            'class' => 'form-control',
                            'placeholder' => 'Адрес работы'
                        )); ?>
                        <?php echo $form->error($model,'workAddress'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'post', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'post', array(
                            'id' => 'post',
                            'class' => 'form-control',
                            'placeholder' => 'Должность'
                        )); ?>
                        <?php echo $form->error($model,'post'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'contact', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'contact', array(
                            'id' => 'contact',
                            'class' => 'form-control',
                            'placeholder' => 'Контактные данные'
                        )); ?>
                        <?php echo $form->error($model,'contact'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'snils', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-5">
                        <?php echo $form->textField($model,'snils', array(
                            'id' => 'snils',
                            'class' => 'form-control',
                            'placeholder' => 'СНИЛС'
                        )); ?>
                        <?php echo $form->error($model,'snils'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'invalidGroup', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->dropDownList($model, 'invalidGroup', array('Нет', 'I', 'II', 'III', 'IV'), array(
                            'id' => 'invalidGroup',
                            'class' => 'form-control'
                        )); ?>
                        <?php echo $form->error($model,'invalidGroup'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="add-patient-submit">
                <?php echo CHtml::ajaxSubmitButton(
                    'Редактировать',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/editcard'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                    $("#patient-medcard-edit-form").trigger("success", [data, textStatus, jqXHR])
                                }'
                    ),
                    array(
                        'class' => 'btn btn-success'
                    )
                ); ?>
            </div>
        </div>
    <?php $this->endWidget(); ?>
</div>
<div class="modal fade error-popup" id="errorAddPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Ошибка!</h4>
            </div>
            <div class="modal-body">
                <div class="row">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="successAddPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Успешно!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Вы успешно отредактировали карту.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>