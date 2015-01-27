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
<div id="accordionX" class="accordion col-xs-12">
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
<div id="accordionH" class="accordion col-xs-12">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a href="#collapseH" data-parent="#accordionH" data-toggle="collapse" class="accordion-toggle" data-toggle="tooltip" data-placement="right" title="Здесь Вы можете посмотреть историю изменений медицинской карты. Раскройте список и выберите запись для просмотра изменений медкарты."><strong>История медкарты</strong></a>
        </div>
        <div class="accordion-body collapse in" id="collapseH">
            <div class="accordion-inner">

                <?php
                //var_dump($historyPoints);
                //exit();

                foreach ($historyPoints as $key => $point) { ?>
                    <div>
                        <a href="#<?php echo $point['medcard_id']; ?>_<?php echo $point['greeting_id']; ?>_<?php echo $point['template_id']; ?>" class="medcard-history-showlink"><?php echo $point['date_change']; ?> - <?php echo $point['template_name']; ?></a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Секция направлений -->
<?php
    $this->widget('application.modules.hospital.components.widgets.MedicalDirectionsForm', array(
        'currentOmsId' => $currentOmsId,
        'currentDoctorId' => $currentDoctorId
    )); ?>
<!-- Секция комментарии -->
<div id="accordionC" class="accordion col-xs-12">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a href="#collapseC"
               data-parent="#accordionC"
               data-toggle="collapse"
               class="accordion-toggle"
               data-toggle="tooltip"
               data-placement="right" title="Здесь Вы можете посмотреть комментарии к данному пациенту"><strong>Комментарии</strong></a>
        </div>
        <div class="accordion-body collapse in" id="collapseС">
            <div class="accordion-inner">
                <!-- Здесь выведем последний комментарий к данному пользователю-->
                    <div class="greetingCommentBlock">
                    <?php
                    $this->render('application.modules.doctors.components.widgets.views.oneCommentBlock', array(
                        'doctorComment' => $doctorComment,
                        'numberDoctorComments' => $numberDoctorComments
                    ));
                    ?>
                    </div>
                <button type="button" class="btn btn-success" id="addCommentButton">Добавить</button>
            </div>
        </div>
    </div>
</div>
    <!-- Выведем поп-ап для просмотра всех комментариев -->
    <div class="modal fade error-popup" id="allCommentsPopup">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Комментарии к пациенту <span id="commentsPatientName"><strong></strong></span></h4>
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
    <!-- Поп-ап для создания/редактирования комментария -->
    <div class="modal fade error-popup" id="addCommentPopup">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Добавить комментарий</h4>
                </div>
                <?php
                $commentForm = $this->beginWidget('CActiveForm', array(
                    'focus' => array($addCommentModel,'description'),
                    'id' => 'comment-add-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => true,
                    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/cabinets/add'),
                    'htmlOptions' => array(
                        'class' => 'form-horizontal col-xs-12',
                        'role' => 'form'
                    )
                ));
                ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <?php echo $commentForm->hiddenField($addCommentModel,'commentId', array(
                                'id' => 'commentId',
                                'class' => 'form-control'
                            )); ?>
                            <?php echo $commentForm->hiddenField($addCommentModel,'forPatientId', array(
                                'id' => 'forPatientId',
                                'class' => 'form-control'
                            )); ?>
                            <div class="form-group">
                                <?php echo $commentForm->labelEx($addCommentModel,'commentText', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $commentForm->textArea($addCommentModel,'commentText', array(
                                        'id' => 'commentText',
                                        'class' => 'form-control',
                                        'placeholder' => 'Текст'
                                    )); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <?php echo CHtml::ajaxSubmitButton(
                        'Добавить',
                        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/doctors/comment/add'),
                        array(
                            'success' => 'function(data, textStatus, jqXHR) {
                                $("#comment-add-form").trigger("success", [data, textStatus, jqXHR])
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
    <div class="modal fade error-popup" id="editCommentPopup">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Редактировать комментарий</h4>
                </div>
                <?php
                $commentForm= $this->beginWidget('CActiveForm', array(
                    'focus' => array($addCommentModel,'shortName'),
                    'id' => 'comment-edit-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => true,
                    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/cabinets/edit'),
                    'htmlOptions' => array(
                        'class' => 'form-horizontal col-xs-12',
                        'role' => 'form'
                    )
                ));
                ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <?php echo $commentForm->hiddenField($addCommentModel,'commentId', array(
                                'id' => 'commentId',
                                'class' => 'form-control'
                            )); ?>
                            <?php echo $commentForm->hiddenField($addCommentModel,'forPatientId', array(
                                'id' => 'forPatientId',
                                'class' => 'form-control'
                            )); ?>
                            <div class="form-group">
                                <?php echo $commentForm->labelEx($addCommentModel,'commentText', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $commentForm->textArea($addCommentModel,'commentText', array(
                                        'id' => 'commentText',
                                        'class' => 'form-control',
                                        'placeholder' => 'Текст'
                                    )); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <?php echo CHtml::ajaxSubmitButton(
                        'Сохранить',
                        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/doctors/comment/edit'),
                        array(
                            'success' => 'function(data, textStatus, jqXHR) {
                                $("#comment-edit-form").trigger("success", [data, textStatus, jqXHR])
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
    <div class="modal fade error-popup" id="successPopup">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Успешно!</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <p>

                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade error-popup" id="errorAddCommentPopup">
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
    <div class="modal fade error-popup" id="errorEditCommentPopup">
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