<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/writePatient.js" ></script>
<?php if($calendarType == 0) { ?>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/calendar.js" ></script>
<?php } elseif($calendarType == 1) { ?>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/organizer.js" ></script>
<?php } ?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/jquery-json.js" ></script>
<script type="text/javascript">
    globalVariables.months = [
        'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
    ];
</script>
<?php if(Yii::app()->user->checkAccess('writePatient')) { ?>
<div class="row">
    <?php
    $this->widget('application.modules.reception.components.widgets.WritePatientTabMenu',
        array(
            'callcenter' => 0
        )
    ); ?>
</div>
<?php } ?>
