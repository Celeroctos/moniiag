<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/767e5633/jquery.yiiactiveform.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/searchAddPatient.js" ></script>
<h4>Редактирование ОМС пациента</h4>
<p class="text-left">
    Заполните поля формы, чтобы отредактировать ОМС существующего пациента <span class="text-danger bold">(<?php echo $fio; ?>, полис №<?php echo $policy_number; ?>)</span>
</p>
<div class="row default-padding">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'patient-oms-edit-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/editoms'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
        <div class="row">
            <div class="col-xs-6">
                <?php echo $form->hiddenField($model,'id', array(
                    'id' => 'id',
                    'class' => 'form-control'
                )); ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'policy', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-5">
                        <?php echo $form->textField($model,'policy', array(
                            'id' => 'policy',
                            'class' => 'form-control',
                            'placeholder' => 'Номер ОМС'
                        )); ?>
                        <?php echo $form->error($model,'policy'); ?>
                        <span class="help-block">Номер ОМС может состоять из цифр и пробелов</span>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'lastName', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'lastName', array(
                            'id' => 'lastName',
                            'class' => 'form-control',
                            'placeholder' => 'Фамилия'
                        )); ?>
                        <?php echo $form->error($model,'lastName'); ?>
                        <span class="help-block">Фамилия может состоять из кириллицы и дефисов (двойные фамилии)</span>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'firstName', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'firstName', array(
                            'id' => 'firstName',
                            'class' => 'form-control',
                            'placeholder' => 'Имя'
                        )); ?>
                        <?php echo $form->error($model,'firstName'); ?>
                        <span class="help-block">Имя может состоять из кириллицы и дефисов</span>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'middleName', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'middleName', array(
                            'id' => 'middleName',
                            'class' => 'form-control',
                            'placeholder' => 'Отчество'
                        )); ?>
                        <?php echo $form->error($model,'middleName'); ?>
                        <span class="help-block">Отчество может состоять из кириллицы и дефисов. Это необязательное поле.</span>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'gender', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-5">
                        <?php echo $form->dropDownList($model, 'gender', array('Женский', 'Мужской'), array(
                            'id' => 'gender',
                            'class' => 'form-control'
                        )); ?>
                        <?php echo $form->error($model,'gender'); ?>
                    </div>
                </div>
                <div class="form-group date-ctrl-buttons">
                    <span class="col-xs-3">
                    </span>
                    <div class="btn-group col-xs-5">
                        <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-plus glyphicon up-day-button">

                        </button>
                        <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-plus glyphicon month-button up-month-button">

                        </button>
                        <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-plus glyphicon year-button up-year-button" >

                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'birthday', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-6 input-group date" id="birthday-cont">
                        <?php echo $form->hiddenField($model,'birthday', array(
                            'id' => 'birthday',
                            'class' => 'form-control',
                            'placeholder' => 'Формат гггг-мм-дд'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                    <div class="clear"></div>
                    <div class="row subcontrol form-inline col-xs-3 col-lg-offset-2">
                        <div class="col-xs-3">
                            <input type="text" name="day" placeholder="ДД" class="form-control day">
                        </div>
                        <div class="col-xs-3">
                            <input type="text" name="month" placeholder="ММ" class="form-control month">
                        </div>
                        <div class="col-xs-4">
                            <input type="text" name="year" placeholder="ГГГГ" class="form-control year">
                        </div>
                    </div>
                </div>
                <div class="form-group date-ctrl-buttons">
                    <span class="col-xs-3">
                    </span>
                    <div class="btn-group col-xs-5">
                        <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-minus glyphicon down-day-button"></button>
                        <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-minus glyphicon month-button down-month-button"></button>
                        <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-minus glyphicon year-button down-year-button"></button>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">

            </div>
        </div>
        <div class="form-group">
            <div class="add-patient-submit">
                <?php echo CHtml::ajaxSubmitButton(
                    'Редактировать',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/editoms'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                    $("#patient-oms-edit-form").trigger("success", [data, textStatus, jqXHR])
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