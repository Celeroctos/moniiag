<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/settings/shedule.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<h4>Настройки расписания</h4>
<div class="row" id="shedule-settings-block">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'focus' => array($model,'timePerPatient'),
        'id' => 'shedule-settings-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/modules/shedulesettingsedit'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
        <div class="form-group">
            <?php echo $form->labelEx($model,'timePerPatient', array(
                'class' => 'col-xs-2 control-label'
            )); ?>
            <div class="col-xs-4">
                <?php echo $form->textField($model,'timePerPatient', array(
                    'id' => 'timePerPatient',
                    'class' => 'form-control',
                    'placeholder' => 'Норма времени на одного пациента'
                )); ?>
                <?php echo $form->error($model,'timePerPatient'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'firstVisit', array(
                'class' => 'col-xs-2 control-label'
            )); ?>
            <div class="col-xs-4">
                <?php echo $form->textField($model,'firstVisit', array(
                    'id' => 'firstVisit',
                    'class' => 'form-control',
                    'placeholder' => 'Количество первичных осмотров'
                )); ?>
                <?php echo $form->error($model,'firstVisit'); ?>
            </div>
        </div>
        
        <div class="form-group">
            <?php echo $form->labelEx($model,'quote', array(
                'class' => 'col-xs-2 control-label'
            )); ?>
            <div class="col-xs-4">
                <?php echo $form->textField($model,'quote', array(
                    'id' => 'quote',
                    'class' => 'form-control',
                    'placeholder' => 'Квота на запись на будущие числа'
                )); ?>
                <?php echo $form->error($model,'quote'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'shiftType', array(
                'class' => 'col-xs-2 control-label'
            )); ?>
            <div class="col-xs-4">
                <?php echo $form->dropDownList($model, 'shiftType', array('По четным / нечетным дням недели', 'По четным / нечетным числам месяца'), array(
                    'id' => 'shiftType',
                    'class' => 'form-control'
                )); ?>
                <?php echo $form->error($model,'shiftType'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'calendarType', array(
                'class' => 'col-xs-2 control-label'
            )); ?>
            <div class="col-xs-4">
                <?php echo $form->dropDownList($model, 'calendarType', array('Обычный календарь', 'Стиль органайзера'), array(
                    'id' => 'calendarType',
                    'class' => 'form-control'
                )); ?>
                <?php echo $form->error($model,'calendarType'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo CHtml::ajaxSubmitButton(
                'Сохранить',
                CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/modules/shedulesettingsedit'),
                array(
                    'success' => 'function(data, textStatus, jqXHR) {
                                $("#shedule-settings-form").trigger("success", [data, textStatus, jqXHR])
                            }'
                ),
                array(
                    'class' => 'btn btn-success'
                )
            ); ?>
        </div>
    <?php $this->endWidget(); ?>
</div>
<div class="modal fade" id="addShiftPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить смену</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'shift-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/modules/addshift'),
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
                        
                        
                    <?php
                        echo $form->labelEx($shiftModel,'timeBegin', array(
                                'class' => 'col-xs-3 control-label'
                            ));
                    ?>
                    <div class="col-xs-9 input-group date time-control" id="add-timeBegin-cont">
                        <?php echo $form->hiddenField($shiftModel,'timeBegin', array(
                            'id' => 'timeBegin',
                            'class' => 'form-control',
                            'placeholder' => 'Формат (чч:мм)'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="time-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                :
                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                            </div>
                            <div class="time-ctrl-down-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                    <?php echo $form->error($shiftModel,'timeBegin'); ?>
                </div>
                        

                    <div class="form-group">
                    <?php
                        echo $form->labelEx($shiftModel,'timeEnd', array(
                                'class' => 'col-xs-3 control-label'
                            ));
                    ?>
                    <div class="col-xs-9 input-group date time-control" id="add-timeEnd-cont">
                        <?php echo $form->hiddenField($shiftModel,'timeEnd', array(
                            'id' => 'timeEnd',
                            'class' => 'form-control',
                            'placeholder' => 'Формат (чч:мм)'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="time-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                :
                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                            </div>
                            <div class="time-ctrl-down-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo $form->error($shiftModel,'timeEnd'); ?>
                    
                </div>
                        
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/modules/addshift'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#shift-add-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade error-popup" id="errorSheduleSettingsEditPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
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
<div class="modal fade error-popup" id="successSheduleSettingsEditPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Успешно!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Настройки модуля сохранены.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<h4>Список смен</h4>
<p>Вы можете добавить, удалить или отредактировать смены, по которым работают врачи.</p>
<table id="shifts"></table>
<div id="shiftsPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addShift">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editShift">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default" id="deleteShift">Удалить запись</button>
</div>

<div class="modal fade" id="editShiftPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать смену</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'shift-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/modules/editshift'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
            <div class="modal-body">
                <?php echo $form->hiddenField($shiftModel,'id', array(
                    'id' => 'id',
                ));?>
                <div class="row">
                    <div class="col-xs-12">
                <div class="form-group">
                    <?php
                        echo $form->labelEx($shiftModel,'timeBegin', array(
                                'class' => 'col-xs-3 control-label'
                            ));
                    ?>
                    <div class="col-xs-9 input-group date time-control" id="edit-timeBegin-cont">
                        <?php echo $form->hiddenField($shiftModel,'timeBegin', array(
                            'id' => 'timeBegin',
                            'class' => 'form-control',
                            'placeholder' => 'Формат (чч:мм)'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="time-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                :
                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                            </div>
                            <div class="time-ctrl-down-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo $form->error($shiftModel,'timeBegin'); ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $form->labelEx($shiftModel,'timeEnd', array(
                                'class' => 'col-xs-3 control-label'
                            ));
                    ?>
                    <div class="col-xs-9 input-group date time-control" id="edit-timeEnd-cont">
                        <?php echo $form->hiddenField($shiftModel,'timeEnd', array(
                            'id' => 'timeEnd',
                            'class' => 'form-control',
                            'placeholder' => 'Формат (чч:мм)'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="time-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                :
                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                            </div>
                            <div class="time-ctrl-down-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo $form->error($shiftModel,'timeEnd'); ?>
                    
                </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/modules/editshift'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#shift-edit-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade error-popup" id="errorAddShiftPopup">
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
