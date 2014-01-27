<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/restshedule.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<h4>Календарь выходных дней</h4>
<p>Раздел предлагает задать выходные дни в расписании врачей.</p>
<div class="row">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'restcalendar-shedule-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/shedule/restedit'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-7',
            'role' => 'form'
        )
    ));
    ?>
        <div class="form-group">
            <?php echo $form->labelEx($model,'restDays', array(
                'class' => 'col-xs-6 control-label'
            )); ?>
            <div class="col-xs-6">
                <?php echo $form->dropDownList($model, 'restDays', $restDays, array(
                    'id' => 'restDays',
                    'class' => 'form-control',
                    'multiple' => 'multiple',
                    'options' => $selectedDays
                )); ?>
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
                    'class' => 'btn btn-success',
                    'id' => 'submitRestDays'
                )
            ); ?>
        </div>
    <?php $this->endWidget(); ?>
</div>
<p>Задайте помимо этого отдельные праздничные дни: (текущий год - <strong class="currentYear"><?php echo $year; ?></strong>)</p>
<div class="row default-padding-left yearBtnCont">
    <button id="showPrevYear" class="btn btn-primary <?php echo $displayPrev ? '' : 'no-display'; ?>" type="button">
        <span class="glyphicon glyphicon-arrow-left"></span> Показать предыдущий год
    </button>
    <button id="showNextYear" class="btn btn-primary" type="button" >
        Показать следующий год <span class="glyphicon glyphicon-arrow-right"></span>
    </button>
</div>
<div class="row">
    <table class="calendarTable">
        <?php for($i = 0; $i < 3; $i++) { ?>
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
    <input type="button" value="Сохранить" id="submitHolidays" class="btn btn-success">
</div>