<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/doctors/patient.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/doctors/categories.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript">
    globalVariables.patientsInCalendar = <?php echo $patientsInCalendar; ?>;
</script>
<?php if(Yii::app()->user->checkAccess('canViewPatientList')) { ?>
<div class="row">
    <div class="col-xs-7">
    <!-- Выводим информацию о карте -->
    <?php
            $this->widget('application.modules.doctors.components.widgets.MedcardContentWidget', array(
                'medcard' => $medcard,
                'historyPoints' => $historyPoints,
                'primaryDiagnosis' => $primaryDiagnosis,
                'secondaryDiagnosis' => $secondaryDiagnosis,
                'currentPatient' => $currentPatient,
                'currentSheduleId' => $currentSheduleId,
                'canEditMedcard' => $canEditMedcard
            ));
    ?>
    </div>
    <div class="col-xs-5">
        <h5 class="patient-list-h5"><strong>Список пациентов на <span class="text-danger"><?php echo $currentDate; ?></span></strong></h5>
        <?php
        $filterForm = $this->beginWidget('CActiveForm', array(
            'id' => 'change-date-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/doctors/shedule/view'),
            'htmlOptions' => array(
                'class' => 'form-horizontal col-xs-12',
                'role' => 'form'
            )
        ));
        ?>
        <div class="form-group">
            <div class="col-xs-6 input-group shedule-datepicker" id="date-cont">
                <?php echo $filterForm->hiddenField($filterModel,'date', array(
                    'id' => 'filterDate',
                    'class' => 'form-control',
                    'placeholder' => 'Формат гггг-мм-дд'
                )); ?>
                <div class="subcontrol">
                    <div class="date-ctrl-up-buttons">
                        <div class="btn-group">
                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-day-button"></button>
                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon month-button up-month-button"></button>
                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon year-button up-year-button" ></button>
                        </div>
                    </div>
                    <div class="form-inline subfields">
                        <input type="text" name="day" placeholder="ДД" class="form-control day">
                        <input type="text" name="month" placeholder="ММ" class="form-control month">
                        <input type="text" name="year" placeholder="ГГГГ" class="form-control year">
                    </div>
                    <div class="date-ctrl-down-buttons">
                        <div class="btn-group">
                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-day-button"></button>
                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon month-button down-month-button"></button>
                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon year-button down-year-button" ></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group" id="showPatientsSubmit-cont">
            <?php echo CHtml::submitButton(
                'Показать',
                array(
                    'class' => 'btn btn-success',
                    'id' => 'showPatientsSubmit'
                )
            );
            ?>
        </div>
        <?php $this->endWidget(); ?>
        <div class="row">
            <div class="col-xs-12 borderedBox">
                <table id="omsSearchWithCardResult" class="table table-condensed table-hover">
                    <thead>
                    <tr class="header">
                        <td>
                            ФИО
                        </td>
                        <td>
                            Время приёма
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                        <?php if($currentPatient !== false && Yii::app()->user->checkAccess('canPrintMovement')) { ?>
                        <td>
                        </td>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($patients as $key => $patient) { ?>
                        <tr <?php echo $patient['id'] == $currentSheduleId ? "class='success activeGreeting'" : ''; ?>>
                            <td>
                                <?php echo CHtml::link($patient['fio'], array('/doctors/shedule/view?cardid='.$patient['medcard_id'].'&date='.$filterModel->date.'&rowid='.$patient['id'])); ?>
                            </td>
                            <td>
                                <?php echo $patient['patient_time']; ?>
                            </td>
                            <td>
                                <?php echo CHtml::link('<span class="glyphicon glyphicon-edit"></span>', array('/reception/patient/editcardview/?cardid='.$patient['medcard_id']), array('title' => 'Посмотреть медкарту')); ?>
                            </td>
                            <td>
                                <?php echo ($patient['is_beginned'] == 1 || $patient['is_accepted'] == 1) ? '' : CHtml::link('<span class="glyphicon glyphicon-flash"></span>', array('/doctors/shedule/acceptbegin/?id='.$patient['id']), array('title' => 'Начать приём')); ?>
                            </td>
                            <td>
                                <?php echo ($patient['is_accepted'] == 1 ||$patient['is_beginned'] != 1) ? '' : CHtml::link('<span class="glyphicon glyphicon-flag"></span>', array('/doctors/shedule/acceptcomplete/?id='.$patient['id']), array('title' => 'Закончить приём')); ?>
                            </td>
                            <?php if(Yii::app()->user->checkAccess('canPrintMovement')) { ?>
                            <td>
                                <?php echo $patient['id'] == $currentSheduleId ? CHtml::link('<span class="glyphicon glyphicon-print"></span>', '#'.$patient['id'],
                                                                                                                array('title' => 'Печать листа приёма',
                                                                                                                      'class' => 'print-greeting-link')) : ''; ?>
                            </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<?php if(Yii::app()->user->checkAccess('canViewMedcardHistory')) { ?>
<?php if($currentPatient !== false) { ?>
<div class="modal fade error-popup" id="historyPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">История медкарты <span class="medcardNumber"></span> за <span class="historyDate"></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php
                    $this->widget('application.modules.doctors.components.widgets.CategorieViewWidget',array(
                        'currentPatient' => $currentPatient,
                        'templateType' => 0,
                        'prefix' => 'history',
                        'withoutSave' => 1,
                        'canEditMedcard' => 0
                    )); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<?php if(Yii::app()->user->checkAccess('canAddNewGuideValue')) { ?>
<div class="modal fade error-popup" id="addValuePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить значение в справочник</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php
                    $addForm = $this->beginWidget('CActiveForm', array(
                        'id' => 'add-value-form',
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => true,
                        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/guides/addinguide'),
                        'htmlOptions' => array(
                            'class' => 'form-horizontal col-xs-12',
                            'role' => 'form'
                        )
                    ));
                    ?>
                    <div class="form-group col-xs-3">
                        <?php
                            echo $addForm->labelEx($addModel,'value', array(
                                'class' => 'control-label'
                            ));
                        ?>
                    </div>
                    <div class="form-group col-xs-7">
                        <?php
                            echo $addForm->hiddenField($addModel,'guideId', array(
                                'id' => 'guideId',
                                'class' => 'form-control',
                                'placeholder' => ''
                            ));
                            echo $addForm->textField($addModel,'value', array(
                                'id' => 'addGuideValue',
                                'class' => 'form-control',
                                'placeholder' => ''
                            ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/guides/addinguide'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                    $("#add-value-form").trigger("success", [data, textStatus, jqXHR])
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
<?php } ?>
<div class="modal fade error-popup" id="errorPopup">
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
<?php } ?>
<div class="modal fade error-popup" id="noticePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Уведомление</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Все изменения в медкарте будут сохранены автоматически перед печатью листа приёма.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="printPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Печать листа приёма</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Вы хотите распечатать лист текущего приёма?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Да</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Нет</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="successDiagnosisPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Успешное сохранение диагнозов</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Диагнозы для текущего приёма успешно сохранены.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>