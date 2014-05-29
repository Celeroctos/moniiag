<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/restshedule.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/twocolumncontrol.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<h4>Календарь выходных дней на <strong class="currentYear"><?php echo $year; ?></strong> год</h4>
<div class="row">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'restcalendar-shedule-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/shedule/restedit'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
    <div class="form-group">
        <?php echo $form->labelEx($model,'restDays', array(
            'class' => 'col-xs-4 control-label'
        )); ?>
        <div class="col-xs-8">
            <!-- Обёртка, чтобы можно было по id обращаться-->
            <div id="weekEndSelector" class="col-xs-12">
            <?php
            echo $form->checkBoxList($model, 'restDays',$restDays,
                        array(
                        'id' => 'restDays',
                        'separator'=>' '
                        )              
            );
            ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <?php echo CHtml::ajaxSubmitButton(
            'Сохранить',
            CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/shedule/restedit'),
            array(
                'success' => 'function(data, textStatus, jqXHR) {
$("#restcalendar-shedule-form").trigger("success", [data, textStatus, jqXHR])
}'
            ),
            array(
                'class' => 'btn btn-success no-display',
                'id' => 'submitRestDays'
            )
        ); ?>
    </div>
    <?php $this->endWidget(); ?>
</div>
<div class="row">
    <div class="twoColumnList col-xs-12" id="doctorsSelector" style="text-align: center; vertical-align: middle;">
            <select multiple="multiple" class="twoColumnListFrom" style="width:300px;height:300">
                <?php
                foreach ($doctors as $oneDoctor)
                {
                    ?>
                        <option value="<?php echo $oneDoctor['id']; ?>"><?php
                            echo ($oneDoctor['last_name'].' '.$oneDoctor['first_name'].' '.$oneDoctor['middle_name']);

                            ?></option>
                    <?php
                }
                ?>
            </select>
        <div class="TCLButtonsContainer" style="text-align: center;">
            <span class = "glyphicon glyphicon-forward twoColumnAddAllBtn"></span>
            <span class = "glyphicon glyphicon-backward twoColumnRemoveAllBtn"></span>

            <span class = "glyphicon glyphicon-arrow-right twoColumnAddBtn"></span>
            <span class = "glyphicon glyphicon-arrow-left twoColumnRemoveBtn"></span>


        </div>
            <select multiple="multiple" class="twoColumnListTo" style="width:300px;height:300">
                <!-- Здесь будут выбранные опции -->

            </select>
        <input type="hidden" id="doctorsToChangeTimetable" class="twoColumnHidden"></input>
    </div>
</div>
<div class="row">
    <input type="radio" id="dayType" name="dayType" value = "1" checked>Выходной<br>
    <input type="radio" id="dayType" name="dayType" value = "2">Отпуск<br>
    <input type="radio" id="dayType" name="dayType" value = "3">Болезнь<br>
    <input type="radio" id="dayType" name="dayType" value = "4">Командировка<br>
</div>
<div class="row">
    <table class="calendarTable">

        <script type="text/javascript">
            globalVariables.weekEndDays = <?php echo $selectedDaysJson; ?>;
            $(document).ready(function() {
                $('.calendarTable').trigger('print');
                $('.calendarTable').trigger('refresh');
            });
        </script>

        <?php if (false) for($i = 0; $i < 3; $i++) { ?>
            <tr>
                <?php for($j = 0; $j < 4; $j++) { ?>
                    <td class="calendarTd">
                        <h6></h6>
                        <script type="text/javascript">
                            $(document).ready(function() {
                                $('.calendarTable').trigger('showCalendar', [<?php echo $restCalendars; ?>, <?php echo $i; ?>, <?php echo $j; ?>, <?php echo $year; ?>, <?php echo $selectedDaysJson; ?>])
                            });
                        </script>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
    <!--<input type="button" value="Сохранить" id="submitHolidays" class="btn btn-success">-->
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
                    <p>Расписание успешно изменено.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="row default-padding-left yearBtnCont">
    <button id="showPrevYear" class="btn btn-primary" type="button">
        <span class="glyphicon glyphicon-arrow-left"></span><span> </span><span id="previousYearBtnCaption">Предыдущий год
    </button>
    <button id="showNextYear" class="btn btn-primary" type="button" >
        <span id="nextYearBtnCaption">Следующий год</span><span> </span><span class="glyphicon glyphicon-arrow-right"></span>
    </button>
    <button type="button" class="btn btn-primary editCalendar">Сохранить</button>
</div>