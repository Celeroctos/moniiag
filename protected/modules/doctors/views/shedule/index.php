<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/doctors/patient.js"></script>
<script type="text/javascript">
    globalVariables.patientsInCalendar = <?php echo $patientsInCalendar; ?>;
</script>
<div class="row">
    <div class="col-xs-7">
        <?php if($currentPatient !== false) { ?>
        <div class="col-xs-12">
            <div id="accordionX" class="accordion">
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a href="#collapseX" data-parent="#accordionX" data-toggle="collapse" class="accordion-toggle"><strong>Реквизитная информация</strong></a>
                    </div>
                    <div class="accordion-body collapse in" id="collapseX">
                        <div class="accordion-inner">
                            <p>
                                ФИО:<strong> <?php echo $medcard['last_name']; ?> <?php echo $medcard['first_name']; ?> <?php echo $medcard['middle_name']; ?></strong><br />
                                Возраст:<strong> <?php echo $medcard['full_years']; ?></strong><br/>
                                Адрес:<strong> <?php echo $medcard['address']; ?></strong><br/>
                                Место работы:<strong> <?php echo $medcard['work_place']; ?>, <?php echo $medcard['work_address']; ?></strong><br/>
                                Телефон:<strong> <?php echo $medcard['contact']; ?></strong><br/>
                                № амбулаторной карты:<strong> <?php echo $medcard['card_number']; ?></strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div id="accordionH" class="accordion">
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a href="#collapseH" data-parent="#accordionH" data-toggle="collapse" class="accordion-toggle"><strong>История медкарты</strong></a>
                        <span class="help-block">
                            Здесь Вы можете посмотреть историю изменений медицинской карты. Раскройте список и выберите запись для просмотра изменений медкарты.
                        </span>
                    </div>
                    <div class="accordion-body collapse in" id="collapseH">
                        <div class="accordion-inner">
                            <?php foreach ($historyPoints as $key => $point) { ?>
                           <div>
                               <a href="#">
                                    <?php echo $point['change_date']; ?>
                               </a>
                           </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->widget('application.modules.doctors.components.widgets.CategorieViewWidget',array(
            'currentPatient' => $currentPatient,
            'templateType' => 0
        )); ?>
            <?php if($medcard['gender'] == 0) { ?>
                <h5><strong>Ведение беременности</strong></h5>
                <?php $this->widget('application.modules.doctors.components.widgets.CategorieViewWidget',array(
                    'currentPatient' => $currentPatient,
                    'templateType' => 1
                )); ?>
            <?php }?>
        <?php } ?>
    </div>
    <div class="col-xs-5">
        <h5><strong>Список пациентов на <span class="text-danger"><?php echo $currentDate; ?></span></strong></h5>
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
            <?php echo $filterForm->labelEx($filterModel,'date', array(
                'class' => 'col-xs-3 control-label'
            )); ?>
            <div class="col-xs-6 input-group date" id="date-cont">
                <?php echo $filterForm->textField($filterModel,'date', array(
                    'id' => 'filterDate',
                    'class' => 'form-control',
                    'placeholder' => 'Формат гггг-мм-дд'
                )); ?>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
        <?php echo CHtml::submitButton(
            'Показать',
            array(
                'class' => 'btn btn-success no-display',
                'id' => 'showPatientsSubmit'
            )
        );
        ?>
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
                            Посмотреть карту
                        </td>
                        <td>
                            Закончить приём
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($patients as $key => $patient) { ?>
                        <tr <?php echo $patient['medcard_id'] == $currentPatient ? "class='success'" : ''; ?>>
                            <td>
                                <?php echo CHtml::link($patient['fio'], array('/doctors/shedule/view?cardid='.$patient['medcard_id'].'&date='.$filterModel->date)); ?>
                            </td>
                            <td>
                                <?php echo $patient['patient_time']; ?>
                            </td>
                            <td>
                                <?php echo CHtml::link('<span class="glyphicon glyphicon-edit"></span>', array('/reception/patient/editcardview/?cardid='.$patient['medcard_id'])); ?>
                            </td>
                            <td>
                                <?php echo $patient['is_accepted'] == 1 ? '' : CHtml::link('<span class="glyphicon glyphicon-flag"></span>', array('/doctors/shedule/acceptcomplete/?id='.$patient['id'])); ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>