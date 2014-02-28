<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/restshedule.js" ></script>
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
<p>Дополнительно выделите праздничные и нерабочие дни:</p>
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
    <!--<input type="button" value="Сохранить" id="submitHolidays" class="btn btn-success">-->
</div>
<div class="row default-padding-left yearBtnCont">
    <button id="showPrevYear" class="btn btn-primary" type="button">
        <span class="glyphicon glyphicon-arrow-left"></span><span> </span><span id="previousYearBtnCaption">Предыдущий год
    </button>
    <button id="showNextYear" class="btn btn-primary" type="button" >
        <span id="nextYearBtnCaption">Следующий год</span><span> </span><span class="glyphicon glyphicon-arrow-right"></span>
    </button>
</div>