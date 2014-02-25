
        <?php if($currentPatient !== false) { ?>
        <script type="text/javascript">
            globalVariables.medcardNumber = '<?php echo $medcard['card_number']; ?>';
            globalVariables.addValueUrl = ''; // ID текущего справочника, в который добавляем значения
        <?php if(!$canEditMedcard) { ?>
            $(document).ready(function() {
                $('#primaryDiagnosisChooser .choosed span.glyphicon-remove').remove();
                $('#secondaryDiagnosisChooser .choosed span.glyphicon-remove').remove();
            });
        <?php } ?>
        </script>
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
                        <a href="#collapseH" data-parent="#accordionH" data-toggle="collapse" class="accordion-toggle" data-toggle="tooltip" data-placement="right" title="Здесь Вы можете посмотреть историю изменений медицинской карты. Раскройте список и выберите запись для просмотра изменений медкарты."><strong>История медкарты</strong></a>
                    </div>
                    <div class="accordion-body collapse in" id="collapseH">
                        <div class="accordion-inner">
                            <?php foreach ($historyPoints as $key => $point) { ?>
                           <div>
                               <a href="#<?php echo $point['medcard_id']; ?>" class="medcard-history-showlink"><?php echo $point['change_date']; ?></a>
                           </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div id="accordionD" class="accordion">
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a href="#collapseD" data-parent="#accordionD" data-toggle="collapse" class="accordion-toggle red-color" data-toggle="tooltip" data-placement="right" title="Диагноз приёма"><strong>Диагноз приёма (основной и сопутствующие)</strong></a>
                    </div>
                    <div class="accordion-body collapse in" id="collapseD">
                        <div class="accordion-inner">
                            <?php
                            $diagnosisForm = $this->beginWidget('CActiveForm', array(
                                'id' => 'diagnosis-form',
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
                                <label for="onlyLikeDiagnosis" class="col-xs-3 control-label" <?php echo !$canEditMedcard ? 'disabled="disabled"' : ''?>>
                                    Выбирать только из списка "любимых" диагнозов
                                </label>
                                <div class="col-xs-9">
                                    <input type="checkbox" id="onlyLikeDiagnosis">
                                </div>
                            </div>
                            <div class="form-group chooser" id="primaryDiagnosisChooser">
                                <label for="doctor" class="col-xs-3 control-label">Основной диагноз:</label>
                                <div class="col-xs-9">
                                    <input type="text" class="form-control" autofocus id="doctor" placeholder="Начинайте вводить..." <?php echo !$canEditMedcard ? 'disabled="disabled"' : ''?>>
                                    <ul class="variants no-display">
                                    </ul>
                                    <div class="choosed">
                                        <?php foreach($primaryDiagnosis as $dia) { ?>
                                            <span class="item" id="r<?php echo $dia['mkb10_id']; ?>"><?php echo $dia['description']; ?><span class="glyphicon glyphicon-remove"></span></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group chooser" id="secondaryDiagnosisChooser">
                                <label for="doctor" class="col-xs-3 control-label">Сопутствующие диагнозы:</label>
                                <div class="col-xs-9">
                                    <input type="text" class="form-control" autofocus id="doctor" placeholder="Начинайте вводить..." <?php echo !$canEditMedcard ? 'disabled="disabled"' : ''?>>
                                    <ul class="variants no-display">
                                    </ul>
                                    <div class="choosed">
                                        <?php foreach($secondaryDiagnosis as $dia) { ?>
                                            <span class="item" id="r<?php echo $dia['mkb10_id']; ?>"><?php echo $dia['description']; ?><span class="glyphicon glyphicon-remove"></span></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="doctor" class="col-xs-3 control-label">Примечание:</label>
                                <div class="col-xs-9">
                                    <textarea placeholder="" class="form-control" id="diagnosisNote" <?php echo !$canEditMedcard ? 'disabled="disabled"' : ''?>><?php echo $note; ?></textarea>
                                </div>
                            </div>
                            <?php if($canEditMedcard) { ?>
                            <div class="form-group">
                                <input type="button" id="submitDiagnosis" value="Сохранить выбранные диагнозы" class="btn btn-primary">
                            </div>
                            <?php } ?>
                            <?php $this->endWidget(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->widget('application.modules.doctors.components.widgets.CategorieViewWidget',array(
            'currentPatient' => $currentPatient,
            'templateType' => 0,
            'withoutSave' => 0,
            'greetingId' => $currentSheduleId,
            'canEditMedcard' => $canEditMedcard,
            'medcard' => $medcard,
            'currentDate' => $currentDate
        )); ?>
            <?php if($medcard['gender'] == 0) { ?>
                <h5><strong>Ведение беременности</strong></h5>
                <?php $this->widget('application.modules.doctors.components.widgets.CategorieViewWidget',array(
                    'currentPatient' => $currentPatient,
                    'greetingId' => $currentSheduleId,
                    'templateType' => 1,
                    'canEditMedcard' => $canEditMedcard,
                    'medcard' => $medcard,
                    'currentDate' => $currentDate
                )); ?>
            <?php }?>
        <?php } ?>