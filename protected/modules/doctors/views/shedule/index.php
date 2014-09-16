<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/doctors/patient.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/doctors/comments.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/doctors/categories.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/tablecontrol.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/twocolumncontrol.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/ajaxbutton.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript">
    globalVariables.patientsInCalendar = <?php echo $patientsInCalendar; ?>;
    globalVariables.reqDiagnosis = <?php echo CJSON::encode($requiredDiagnosis); ?>;
    globalVariables.year = <?php echo $year; ?>;
    globalVariables.month = <?php echo $month; ?>;
    globalVariables.day = <?php echo $day; ?>;
</script>
<?php if (Yii::app()->user->checkAccess('canViewPatientList')) { ?>
    <div class="row">
        <div class="col-xs-5 null-padding-right">
            <!-- Выводим информацию о карте -->
            <?php
            //var_dump($historyPoints);
            //exit();
            $this->widget('application.modules.doctors.components.widgets.MedcardContentWidget', array(
                'medcard' => $medcard,
                'historyPoints' => $historyPoints,
                'primaryDiagnosis' => $primaryDiagnosis,
                'secondaryDiagnosis' => $secondaryDiagnosis,
                'primaryClinicalDiagnosis' => $primaryClinicalDiagnosis,
                'secondaryClinicalDiagnosis' => $secondaryClinicalDiagnosis,
                'currentPatient' => $currentPatient,
                'currentSheduleId' => $currentSheduleId,
                'canEditMedcard' => $canEditMedcard,
                'doctorComment' => $doctorComment,
                'numberDoctorComments' => $numberDoctorComments,
                'addCommentModel' => $addCommentModel
            ));
            ?>
            <?php if ($templatesChoose == 1) { ?>
                <?php if ($greeting != null && $greeting->is_accepted != 1) { ?>
                    <div class="col-xs-12">
                        <div id="accordionT" class="accordion">
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a href="#collapseT" data-parent="#accordionT" data-toggle="collapse"
                                       class="accordion-toggle" data-toggle="tooltip" data-placement="right"
                                       title="Выбор шаблонов для редактирования"><strong>Выбор шаблонов для
                                            изменения</strong></a>
                                </div>
                                <div class="accordion-body collapse in" id="collapseT">
                                    <div class="accordion-inner">
                                        <form id="templates-choose-form" class="form-horizontal col-xs-12" method="post"
                                              role="form"
                                              action="<?php CHtml::normalizeUrl(Yii::app()->request->baseUrl . '/doctors/shedule/view?cardid=' . $medcard['card_number'] . '&date=' . $currentDate . '&rowid=' . $currentSheduleId); ?>">
                                            <div class="overlayCont">
												<?php foreach ($templatesList as $key => $template) { ?>
													<div class="form-group">
														<input type="checkbox" value="<?php echo $template['id']; ?>"
															   name="templatesList[<?php echo $key; ?>]">
														<label
															class="control-label"><?php echo $template['name']; ?></label>
													</div>
												<?php } ?>
											</div>
                                            <div class="form-group">
                                                <?php echo CHtml::submitButton(
                                                    'Начать приём',
                                                    array(
                                                        'class' => 'btn btn-primary'
                                                    )
                                                ); ?>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row">
                        <h4>Этот приём закрыт. Вы можете просмотреть историю медицинской карты.</h4>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="col-xs-3 null-padding-left null-margin-left borderedBox changeDate-cont">
            <h5 class="patient-choose-date-h5"><strong>Выберите дату:</strong></h5>
            <?php
            $filterForm = $this->beginWidget('CActiveForm', array(
                'id' => 'change-date-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl . '/doctors/shedule/view'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
            <div class="form-group">
                <div class="col-xs-6 input-group shedule-datepicker" id="date-cont">
                    <?php echo $filterForm->hiddenField($filterModel, 'date', array(
                        'id' => 'filterDate',
                        'class' => 'form-control',
                        'placeholder' => 'Формат гггг-мм-дд'
                    )); ?>
                    <div class="subcontrol no-display">
                        <div class="date-ctrl-up-buttons">
                            <div class="btn-group">
                                <button type="button" tabindex="-1"
                                        class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-day-button"></button>
                                <button type="button" tabindex="-1"
                                        class="btn btn-default btn-xs glyphicon-arrow-up glyphicon month-button up-month-button"></button>
                                <button type="button" tabindex="-1"
                                        class="btn btn-default btn-xs glyphicon-arrow-up glyphicon year-button up-year-button"></button>
                            </div>
                        </div>
                        <div class="form-inline subfields">
                            <input type="text" name="day" placeholder="ДД" class="form-control day">
                            <input type="text" name="month" placeholder="ММ" class="form-control month">
                            <input type="text" name="year" placeholder="ГГГГ" class="form-control year">
                        </div>
                        <div class="date-ctrl-down-buttons">
                            <div class="btn-group">
                                <button type="button" tabindex="-1"
                                        class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-day-button"></button>
                                <button type="button" tabindex="-1"
                                        class="btn btn-default btn-xs glyphicon-arrow-down glyphicon month-button down-month-button"></button>
                                <button type="button" tabindex="-1"
                                        class="btn btn-default btn-xs glyphicon-arrow-down glyphicon year-button down-year-button"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group no-display" id="showPatientsSubmit-cont">
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
        </div>
        <div class="col-xs-4">
            <div class="row">
                <div class="col-xs-12 borderedBox">
                    <h5><strong>Легенда:</strong></h5>

                    <p>

                    <div class="legend-icon magenta-block"></div>
                    Приём не начат</p>
                    <p>

                    <div class="legend-icon yellow-block"></div>
                    Приём идёт</p>
                    <p>

                    <div class="legend-icon lightgreen-block"></div>
                    Текущий выбранный приём</p>
                    <p>

                    <div class="legend-icon orange-block"></div>
                    Приём окончен</p>
                </div>
            </div>
            <div class="row">
                <ul class="nav nav-tabs patientListNav">
                    <li <?php echo isset($openedTab) && $openedTab == 0 ? 'class="active"' : ''; ?>><a href="#" id="writedByTime">По записи</a></li>
                    <li <?php echo isset($openedTab) && $openedTab == 1 ? 'class="active"' : ''; ?>><a href="#" id="writedByOrder">Живая очередь</a></li>
                </ul>
                <div id="writedByTimeCont" <?php echo isset($openedTab) && $openedTab == 1 ? 'class="no-display"' : ''; ?>>
                    <h5 class="patient-list-h5">
                        <strong>Список пациентов на <span class="text-danger"><?php echo $currentDate; ?></span></strong><a href="#" id="refreshPatientList" title="Обновить список пациентов"><span class="glyphicon glyphicon-refresh"></span></a><a href="#" id="expandPatientList" title="Показать список пациентов со свободными датами в расписании"><span class="glyphicon glyphicon-resize-full"></span></a><a href="#" class="no-display" id="collapsePatientList" title="Скрыть свободное время в расписании"><span class="glyphicon glyphicon-resize-small"></span></a>
                    </h5>
                    <div class="col-xs-12 borderedBox">
                        <?php
                        // Вызываем виджет списка пациентов
                        $this->widget('application.modules.doctors.components.widgets.PatientListWidget', array(
                            'patients' => ((isset($openedTab) && $openedTab == 0) || !isset($openedTab)) ? $patients : array(),
                            'currentSheduleId' => $currentSheduleId,
                            'currentPatient' => $currentPatient,
                            'filterModel' => $filterModel,
                            'isWaitingLine' => 0,
                            'tableId' => 'doctorPatientList',
                            'patientsDay' => $year.'-'.$month.'-'.$day
                        ));
                        ?>
                    </div>
                </div>
                <div id="writedByOrderCont" <?php echo isset($openedTab) && $openedTab == 0 ? 'class="no-display"' : ''; ?>>
                    <h5 class="patient-list-h5">
                        <strong>Живая очередь на <span class="text-danger"><?php echo $currentDate; ?></span></strong><a href="#" id="refreshWaitingList" title="Обновить список пациентов"><span class="glyphicon glyphicon-refresh"></span></a>
                    </h5>
                    <div class="col-xs-12 borderedBox">
                        <?php
                        // Вызываем виджет списка пациентов
                        $this->widget('application.modules.doctors.components.widgets.PatientListWidget', array(
                            'patients' => ((isset($openedTab) && $openedTab == 1)) ? $patients : array(),
                            'currentSheduleId' => $currentSheduleId,
                            'currentPatient' => $currentPatient,
                            'filterModel' => $filterModel,
                            'isWaitingLine' => 1,
                            'tableId' => 'doctorWaitingList',
                            'patientsDay' => $year.'-'.$month.'-'.$day
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if ($currentPatient !== false) {
        if ($templatesChoose == 0) {
            $counter = 0;
    ?><p><a name="topMainTemplates"></a></p>
            <div class="row col-xs-12">
                <ul class="nav nav-tabs templatesListNav">
                    <?php foreach($templatesList as $key => $template) { ?>
                        <li <?php echo $counter == 0 ? 'class="active"' : ''; ?>>
                            <a href="#" id="t<?php echo $template['id']; ?>">
                                <strong><?php echo $template['name']; ?></strong>
                            </a>
                        </li>
                    <?php
                        $counter++;
                    } ?>
                </ul>

            </div>
            <script>
                //oldAddress = document.location.href;

                //document.location.href=document.location.href+'#topMainTemplates';
               // window.scrollBy(0,-50);
                destinationAnchor = $('a[name=topMainTemplates]');
                if (destinationAnchor!=undefined)
                {
                    destination = $(destinationAnchor)[0].offsetTop;
                    $('body,html').animate({
                        scrollTop: destination
                    }, 599);
                }
            </script>
            <script type="text/javascript">
                if (globalVariables.elementsDependences == undefined)
                {
                    globalVariables.elementsDependences = new Array();
                }
            </script>
            <?php
            $counter = 0;
            foreach ($templatesList as $key => $template) {
                ?>
                <div>

                    <?php
                    $this->widget('application.modules.doctors.components.widgets.CategorieViewWidget', array(
                        'currentPatient' => $currentPatient,
                        'templateType' => 0,
                        'templateId' => $template['id'],
                        'withoutSave' => 0,
                        'greetingId' => $currentSheduleId,
                        'canEditMedcard' => $canEditMedcard,
                        'medcard' => $medcard,
                        'currentDate' => $currentDate,
                        'templatePrefix' => 'a' . $template['id'],
                        'medcardRecordId' => $medcardRecordId,
                        'isActiveTemplate' => $counter == 0,
						//'form' => $formM
                    )); ?>
                </div>
            <?php
                $counter++;
            } ?>
            <?php
            //$this->endWidget();
            ?>
            <?php $counter = 0; if (true){ ?>
            <div class="row col-xs-12">
                <ul class="nav nav-tabs templatesListNav templatesListNavBottom">
                    <?php foreach($templatesList as $key => $template) { ?>
                        <li <?php echo $counter == 0 ? 'class="active"' : ''; ?>>
                            <a href="#" id="t<?php echo $template['id']; ?>">
                                <strong>
                                    <?php echo $template['name']; ?>
                                </strong>
                            </a>
                        </li>
                        <?php
                        $counter++;
                    } ?>
                </ul>
            </div>
                <?php } ?>
			<div class="modal fade error-popup" id="successEditPopup">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title">Успешно!</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<p>Информация успешно сохранена.</p>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
						</div>
					</div>
				</div>
			</div>
            <div class="greetingHR"></div>
            <div id="accordionD" class="accordion">
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a href="#collapseD" data-parent="#accordionD" data-toggle="collapse"
                       class="accordion-toggle red-color" data-toggle="tooltip" data-placement="right"
                       title="Диагноз приёма"><strong>Диагноз приёма (основной и сопутствующие)</strong></a>
                    </div>
                    <div class="accordion-body collapse in" id="collapseD">
                        <div class="accordion-inner">
                            <?php
                            $diagnosisForm = $this->beginWidget('CActiveForm', array(
                                'id' => 'diagnosis-form',
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => true,
                            'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl . '/doctors/shedule/view'),
                                'htmlOptions' => array(
                                    'class' => 'form-horizontal col-xs-12',
                                    'role' => 'form'
                                )
                            ));
                            ?>
                            <div class="form-group">
                                <label for="onlyLikeDiagnosis"
                                   class="col-xs-3 control-label" <?php echo !$canEditMedcard ? 'disabled="disabled"' : '' ?>>
                                    Выбирать только из списка "любимых" диагнозов
                                </label>
                                <div class="col-xs-9">
                                    <input type="checkbox" id="onlyLikeDiagnosis">
                                </div>
                            </div>
                            <div class="form-group chooser" id="primaryDiagnosisChooser">
                            <label for="doctor" class="col-xs-3 control-label">Основной диагноз по МКБ-10:</label>

                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="doctor"
                                       placeholder="Начинайте вводить..." <?php echo !$canEditMedcard ? 'disabled="disabled"' : '' ?>>
                                    <ul class="variants no-display">
                                    </ul>
                                    <div class="choosed">
                                        <?php foreach ($primaryDiagnosis as $dia) { ?>
                                            <span class="item"
                                              id="r<?php echo $dia['mkb10_id']; ?>"><?php echo $dia['description']; ?>
                                            <span class="glyphicon glyphicon-remove"></span></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group chooser" id="complicationsDiagnosisChooser">
                                <label for="doctor" class="col-xs-3 control-label">Осложнения основного диагноза по МКБ-10:</label>

                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="doctor"
                                           placeholder="Начинайте вводить..." <?php echo !$canEditMedcard ? 'disabled="disabled"' : '' ?>>
                                    <ul class="variants no-display">
                                    </ul>
                                    <div class="choosed">
                                        <?php  foreach ($complicatingDiagnosis as $dia) { ?>
                                            <span class="item"
                                                  id="r<?php echo $dia['mkb10_id']; ?>"><?php echo $dia['description']; ?>
                                                <span class="glyphicon glyphicon-remove"></span></span>
                                        <?php  }?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group chooser no-display" id="primaryClinicalDiagnosisChooser">
                            <label for="doctor" class="col-xs-3 control-label">Клинический основной
                                    диагноз:</label>

                                <div class="col-xs-9">
                                    <div class="input-group">
                                    <input type="text" class="form-control" id="clinicalPrimaryDiagnosis"
                                           placeholder="Начинайте вводить..." <?php echo !$canEditMedcard ? 'disabled="disabled"' : '' ?>>
                                    <span class="input-group-addon glyphicon glyphicon-plus"></span>
                                    </div>
                                    <ul class="variants no-display">
                                    </ul>
                                    <div class="choosed">
                                        <?php /*if (false)*/
                                        foreach ($primaryClinicalDiagnosis as $dia) { ?>
                                            <span class="item"
                                              id="r<?php echo $dia['diagnosis_id']; ?>"><?php echo $dia['description']; ?>
                                            <span class="glyphicon glyphicon-remove"></span></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group chooser" id="secondaryDiagnosisChooser">
                            <label for="doctor" class="col-xs-3 control-label">Сопутствующие диагнозы по МКБ-10:</label>

                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="doctor"
                                       placeholder="Начинайте вводить..." <?php echo !$canEditMedcard ? 'disabled="disabled"' : '' ?>>
                                    <ul class="variants no-display">
                                    </ul>
                                    <div class="choosed">
                                        <?php foreach ($secondaryDiagnosis as $dia) { ?>
                                            <span class="item"
                                              id="r<?php echo $dia['mkb10_id']; ?>"><?php echo $dia['description']; ?>
                                            <span class="glyphicon glyphicon-remove"></span></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group chooser" id="secondaryClinicalDiagnosisChooser">
                            <label for="doctor" class="col-xs-3 control-label">Клинические
                                    диагноз / диагнозы:</label>

                                <div class="col-xs-9">
                                    <div class="input-group">
                                    <input type="text" class="form-control" id="clinicalSecondaryDiagnosis" placeholder="Начинайте вводить..." <?php echo !$canEditMedcard ? 'disabled="disabled"' : '' ?>>
                                    <span class="input-group-addon glyphicon glyphicon-plus"></span>
                                    </div>
                                    <ul class="variants no-display">
                                    </ul>
                                    <div class="choosed">
                                        <?php /* if (false) */
                                        foreach ($secondaryClinicalDiagnosis as $dia) {
                                            ?>
                                            <span class="item"
                                              id="r<?php echo $dia['diagnosis_id']; ?>"><?php echo $dia['description']; ?>
                                            <span class="glyphicon glyphicon-remove"></span></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="doctor" class="col-xs-3 control-label">Клинические
                                    диагноз / диагнозы:</label>

                                <div class="col-xs-9">
                                <textarea placeholder="" class="form-control" id="diagnosisNote" <?php echo !$canEditMedcard ? 'disabled="disabled"' : '' ?>><?php echo $note; ?></textarea>
                                </div>
                            </div>
                            <?php if ($canEditMedcard) { ?>
                                <div class="form-group" id="submitDiagnosisContainer">
                                <input type="button" id="submitDiagnosis" value="Сохранить выбранные диагнозы"
                                           class="templateContentSave">
                                </div>
                                <div class="form-group">
                                    <input type="button" id="medcardContentSave" value="Сохранить"
                                           class="btn btn-primary">
									<input type="button" id="printContentButton" value="Сохранить и напечатать"
											class="btn btn-primary">
                                </div>
                            <?php } ?>
                            <?php $this->endWidget(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="greetingHR"></div>
			  <?php
            echo CHtml::link('<span class="glyphicon glyphicon-print"></span>', '#' . $currentSheduleId,
                array('title' => 'Печать рекомендаций',
                    'class' => 'print-recomendation-link'));
            ?>
            <?php
            $counter = 0;
            //var_dump('-------');
            //var_dump($referenceTemplatesList);
            //exit();
            ?>
            <p><a name="topRecomTemplates"></a></p>
            <div class="row col-xs-12">
                <ul class="nav nav-tabs recomTemplatesListNav">
                            <?php foreach($referenceTemplatesList as $key => $template) { ?>
                        <li <?php echo $counter == 0 ? 'class="active"' : ''; ?>>
                            <a href="#" id="rt<?php echo $template['id']; ?>">
                                <strong><?php echo $template['name']; ?></strong>
                            </a>
                        </li>
                        <?php
                        $counter++;
                    } ?>
                </ul>

            </div>
            <?php
            $counter = 0;
            //var_dump($referenceTemplatesList);
            //exit();
            foreach ($referenceTemplatesList as $key => $template) {
                ?>
                <div class="default-margin-top">
                    <?php $this->widget('application.modules.doctors.components.widgets.CategorieViewWidget', array(
                        'currentPatient' => $currentPatient,
                        'templateType' => 1,
                        'templateId' => $template['id'],
                        'withoutSave' => 0,
                        'greetingId' => $currentSheduleId,
                        'canEditMedcard' => $canEditMedcard,
                        'medcard' => $medcard,
                        'currentDate' => $currentDate,
                        'templatePrefix' => 'r' . $template['id'],
                        'medcardRecordId' => $medcardRecordId,
						'isActiveTemplate' => $counter == 0,
					//	'form' => $formM
                    )); ?>
                </div>
                <?php
                $counter++;
            }
			//	$this->endWidget();
            $counter = 0;
			?>
            <div class="row col-xs-12">
                <ul class="nav nav-tabs recomTemplatesListNav recomTemplatesListNavBottom">
                    <?php foreach($referenceTemplatesList as $key => $template) { ?>
                        <li <?php echo $counter == 0 ? 'class="active"' : ''; ?>>
                            <a href="#" id="rt<?php echo $template['id']; ?>">
                                <strong><?php echo $template['name']; ?></strong>
                            </a>
                        </li>
                        <?php
                        $counter++;
                    } ?>
                </ul>

            </div>

        <?php } ?>
    <?php } ?>
<?php } ?>
<?php if (Yii::app()->user->checkAccess('canViewMedcardHistory')) { ?>
    <?php if ($currentPatient !== false) { ?>
        <div class="modal fade error-popup" id="historyPopup">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">История медкарты <span class="medcardNumber"></span> за <span
                                class="historyDate"></span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="nav">
                            <button type="button" class="btn btn-primary" id="prevHistoryPoint"><span class="glyphicon glyphicon-arrow-left"></span>Предыдущая точка истории</button>
                            <button type="button" class="btn btn-primary" id="nextHistoryPoint">Следующая точка истории<span class="glyphicon glyphicon-arrow-right"></span></button>
                        </div>
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
    <?php if (Yii::app()->user->checkAccess('canAddNewGuideValue')) { ?>
        <div class="modal fade error-popup" id="addValuePopup">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Добавить значение в справочник</h4>
                    </div>
                    <?php
                    $addForm = $this->beginWidget('CActiveForm', array(
                        'id' => 'add-value-form',
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => true,
                        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl . '/admin/guides/addinguide'),
                        'htmlOptions' => array(
                            'class' => 'form-horizontal col-xs-12',
                            'role' => 'form',
                            'name' => 'add-value-form'
                        )
                    ));
                    ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-xs-3">
                                <?php
                                echo $addForm->labelEx($addModel, 'value', array(
                                    'class' => 'control-label'
                                ));
                                ?>
                            </div>
                            <div class="form-group col-xs-7">
                                <?php
                                echo $addForm->hiddenField($addModel, 'controlId', array(
                                    'id' => 'controlId',
                                    'class' => 'form-control',
                                    'placeholder' => ''
                                ));
                                echo $addForm->textField($addModel, 'value', array(
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
                            CHtml::normalizeUrl(Yii::app()->request->baseUrl . '/admin/guides/addinguide'),
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
<div class="modal fade error-popup" id="addGreetingComboValuePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавление значения в справочник приёма</h4>
            </div>
            <?php
            $addForm = $this->beginWidget('CActiveForm', array(
                'id' => 'add-greeting-value-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl . '/doctors/patient/addvalueinguide'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form',
                    'name' => 'add-greeting-value-form'
                )
            ));
            ?>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-3">
                        <?php
                        echo $addForm->labelEx($addModel, 'value', array(
                            'class' => 'control-label'
                        ));
                        ?>
                    </div>
                    <div class="form-group col-xs-7">
                        <?php
                        echo $addForm->hiddenField($addModel, 'controlId', array(
                            'id' => 'controlId',
                            'class' => 'form-control',
                        ));
                        echo $addForm->hiddenField($addModel, 'greetingId', array(
                            'id' => 'greetingId',
                            'class' => 'form-control',
                            'value' => $currentSheduleId
                        ));
                        echo $addForm->hiddenField($addModel, 'patientId', array(
                            'id' => 'currentPatientId',
                            'class' => 'form-control',
                            'value' => $currentPatient
                        ));
                        echo $addForm->textField($addModel, 'value', array(
                            'id' => 'addGreetingGuideValue',
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
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl . '/doctors/patient/addvalueinguide'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                            $("#add-greeting-value-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade error-popup" id="addClinicalDiagnosisPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавление значения в клинические диагнозы</h4>
            </div>
            <?php
            $addForm = $this->beginWidget('CActiveForm', array(
                'id' => 'add-clinical-diagnosis-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl . '/doctors/patient/addvalueinguide'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form',
                    'name' => 'add-clinical-diagnosis-form'
                )
            ));
            ?>
            <div class="modal-body">
                <div class="row">
                    <label class="control-label col-xs-4" for="diagnosisName">Название диагноза</label>
                    <div class="form-group col-xs-7">
                        <input id="chooserId" name="chooserId" class="form-control" type="hidden"/>
                        <input id="diagnosisName" name="diagnosisName" class="form-control" placeholder="Введите название диагноза" type="text"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-success" id="addClinicalDiagnosisSubmit">Добавить</button>
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="whatPrinting">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Мастер печати приёма</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Пожалуйста, отметьте - что вы хотите напечатать и нажмите на соответствующую кнопку</p>
                </div>
                <div class="row" id="greetingPrintNeed">
                    <p><input type="checkbox" name="greetingPrintNeed" value="-1">  Приём</p>
                </div>
                <div class="row" id="recommendationTemplatesPrintNeed">
                    <p>

                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-close" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-success" id="printPopupButton" data-dismiss="modal">Печать</button>
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
<div class="modal fade error-popup" id="closeGreetingPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Подтверждение</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success closeGreetingPopupButton" data-dismiss="modal">Закончить</button>
                <button type="button" class="btn btn-close" data-dismiss="modal">Нет</button>
            </div>
        </div>
    </div>
</div>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'patient-medcard-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl . '/reception/patient/editcard'),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
));
?>
<div class="modal fade error-popup" id="editMedcardPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Просмотр данных медкарты пациента</h4>
            </div>
            <div class="modal-body">
                <?php echo $form->hiddenField($modelMedcard, 'cardNumber', array(
                    'id' => 'cardNumber',
                    'class' => 'form-control'
                )); ?>
                <?php
                $this->widget('application.modules.reception.components.widgets.MedcardFormWidget', array(
                    'form' => $form,
                    'model' => $modelMedcard,
                    'privilegesList' => $privilegesList,
                    'template' => 'application.modules.reception.components.widgets.views.MedcardFormWidget'
                ));
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>
<?php
$this->widget('application.modules.reception.components.widgets.MedcardFormWidget', array(
    'form' => $form,
    'model' => $modelMedcard,
    'privilegesList' => $privilegesList,
    'template' => 'application.modules.reception.components.widgets.views.addressEditPopup'
));
?>