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
<h4>Необходимо найти пациента, которого требуется записать на приём:</h4>
<div class="row">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'patient-search-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/reception/patient/search'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
    <div class="col-xs-5">
        <div class="form-group">
            <label for="omsNumber" class="col-xs-4 control-label">Номер полиса</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" autofocus id="omsNumber" placeholder="Номер полиса" title="Номер полиса может состоять из цифр и пробелов">
            </div>
        </div>
        <div class="form-group">
            <label for="cardNumber" class="col-xs-4 control-label">Номер карты</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="cardNumber" placeholder="Номер карты" title="Номер карты вводится в формате номер / год">
            </div>
        </div>
        <div class="form-group">
            <label for="lastName" class="col-xs-4 control-label">Фамилия</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="lastName" placeholder="Фамилия" title="Фамилия может состоять из кириллицы и дефисов (двойные фамилии)">
            </div>
        </div>
    </div>