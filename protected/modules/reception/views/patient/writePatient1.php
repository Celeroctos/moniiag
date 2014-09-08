<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/writePatient.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/motionHistory.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js" ></script>
<?php if(Yii::app()->user->checkAccess('writePatient')) { ?>
<div class="row">
    <?php
    if(isset($callcenter)) {
        $this->widget('application.modules.reception.components.widgets.WritePatientTabMenu',
            array(
                'callcenter' => $callcenter
            )
        ); ?>
        <script type="text/javascript">
            globalVariables.isCallCenter = <?php echo $callcenter; ?>;
        </script>
    <?php } ?>
    <?php if(isset($waitingLine) && $waitingLine) { ?>
        <script type="text/javascript">
            globalVariables.isWaitingLine = 1;
            globalVariables.maxInWaitingLine = <?php echo $maxInWaitingLine; ?>;
        </script>
    <?php } ?>
</div>
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
        <?php
        if (!$callcenter)
        { ?>
        <div class="form-group">
            <label for="omsNumber" class="col-xs-4 control-label">Номер полиса</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" autofocus id="omsNumber" placeholder="Номер полиса" title="Номер полиса может состоять из цифр и пробелов">
            </div>
        </div>
        <?php } ?>

        <div class="form-group">
            <label for="cardNumber" class="col-xs-4 control-label">Номер карты</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="cardNumber" placeholder="Номер карты" title="Номер карты вводится в формате номер / год">
            </div>
        </div>

        <?php
        if (!$callcenter)
        {
        ?>
            <div class="form-group">
                <label for="lastName" class="col-xs-4 control-label">Фамилия</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" id="lastName" placeholder="Фамилия" title="Фамилия может состоять из кириллицы и дефисов (двойные фамилии)">
                </div>
            </div>
        <?php } ?>

        <?php
        if (!$callcenter)
        {
        ?>
            <div class="form-group">
                <label for="firstName" class="col-xs-4 control-label">Имя</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" id="firstName" placeholder="Имя" title="Имя может состоять из кириллицы и дефисов">
                </div>
            </div>
        <?php } ?>

        <?php
        if (!$callcenter)
        {
        ?>
        <div class="form-group">
            <label for="middleName" class="col-xs-4 control-label">Отчество</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="middleName" placeholder="Отчество" title="Отчество может состоять из кириллицы и дефисов. Это необязательное поле.">
            </div>
        </div>
        <?php } ?>

        <?php
        if (!$callcenter)
        {
        ?>
            <div class="form-group">
                <label for="birthday2" class="col-xs-4 control-label required">Дата рождения</label>
                <div id="birthday2-cont" class="col-xs-3 input-group date">
                    <input type="hidden" name="birthday2" placeholder="Формат гггг-мм-дд" class="form-control col-xs-4" id="birthday2">
                <span class="input-group-addon">
                    <span class="glyphicon-calendar glyphicon">
                    </span>
                </span>
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
        <?php } ?>

        <?php if($callcenter) { ?>
            <div class="form-group">
                <label for="canPregnant" class="col-xs-4 control-label">Беременная</label>
                <div class="col-xs-8">
                    <select class="form-control" id="canPregnant">
                        <option value="0">Нет</option>
                        <option value="1">Да</option>
                    </select>
                </div>
            </div>
        <?php } ?>

        <div class="form-group">
            <input type="button" id="patient-search-submit" value="Найти" name="patient-search-submit" class="btn btn-success">
        </div>
    </div>
    <div class="col-xs-7">

        <?php
        if (!$callcenter)
        {
        ?>
            <div class="form-group">
                <label for="serie" class="col-xs-4 control-label">Серия, номер</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" id="serie" placeholder="Серия" data-toggle="tooltip" data-placement="right" title="Серия, номер">
                    <input type="text" class="form-control" id="docnumber" placeholder="Номер документа" data-toggle="tooltip" data-placement="right" title="Номер документа может состоять из цифр">
                </div>
            </div>
        <?php } ?>

        <?php
        if (!$callcenter)
        {
        ?>
            <div class="form-group">
                <label for="addressReg" class="col-xs-4 control-label">Адрес регистрации</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" id="addressReg" placeholder="Адрес регистрации">
                </div>
            </div>
        <?php } ?>

        <?php
        if (!$callcenter)
        {
        ?>
            <div class="form-group">
                <label for="address" class="col-xs-4 control-label">Адрес прописки</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" id="address" placeholder="Адрес прописки">
                </div>
            </div>
        <?php } ?>

        <?php
        if (!$callcenter)
        {
        ?>
            <div class="form-group">
                <label for="snils" class="col-xs-4 control-label">СНИЛС</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" id="snils" placeholder="Формат XXX-XXX-XXX-XX" title="Страховой номер индивидуального лицевого счета гражданина в формате XXX-XXX-XXX-XX, где X - цифра.">
                </div>
            </div>
        <?php } ?>
    </div>
    <?php $this->endWidget(); ?>
</div>
<h4>Найденные пациенты, соответствующие введённым данным:</h4>
<div class="row">
    <div class="col-xs-12 borderedBox">
        <table class="table table-condensed table-hover" id="searchWithCardResult">
            <thead>
            <tr class="header">
		<td class="write-patient-cell">
                    Записать
                </td>
                <td>
                    ФИО пациента
                </td>
                <td>
                    Дата рождения
                </td>
                <td>
                    Номер карты
                </td>

                <?php
                if (!$callcenter)
                {
                ?>
                    <td class="omsNumberCell">
                        Номер полиса ОМС
                    </td>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="row no-display">
	<ul class="pagination content-pagination">
	</ul>
    </div>
</div>
<?php } ?>
<div class="modal fade error-popup" id="errorSearchPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Ничего не найдено</h4>
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
<div class="modal fade error-popup" id="notFoundPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Сообщение</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>По введённым поисковым критериям не найдено ни одного пациента. Вы можете ввести новые данные о пациенте, нажав на кнопку ниже.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="createCard">Создать карту</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="viewHistoryMotionPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">История движения карты пациента</h4>
            </div>
            <div class="modal-body">
                <span id="oms-id" class="no-display"></span>
                <div id="cardMotionHistory" class="row">
                    <table id="motion-history"></table>
                    <div id="motion-historyPager"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'patient-medcard-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/reception/patient/editcard'),
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
                <h4 class="modal-title">Редактирование данных медкарты пациента</h4>
            </div>
            <div class="modal-body">
                <?php echo $form->hiddenField($modelMedcard,'cardNumber', array(
                    'id' => 'cardNumber',
                    'class' => 'form-control'
                )); ?>
                <?php
                $this->widget('application.modules.reception.components.widgets.MedcardFormWidget', array(
                    'form' => $form,
                    'model' => $modelMedcard,
                    'privilegesList' => $privilegesList
                ));
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Редактировать',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/reception/patient/editcard'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
							$("#patient-medcard-edit-form").trigger("success", [data, textStatus, jqXHR])
						}',
						'beforeSend' => 'function(jqXHR, settings) {
							 $("#patient-search-submit").trigger("begin")
						}'
                    ),
                    array(
                        'class' => 'btn btn-success'
                    )
                ); ?>
            </div>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'patient-oms-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/reception/patient/editoms'),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
));
?>
<div class="modal fade error-popup" id="editOmsPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактирование данных полиса пациента</h4>
            </div>
            <div class="modal-body">
                <?php echo $form->hiddenField($modelOms,'id', array(
                    'id' => 'id',
                    'class' => 'form-control'
                )); ?>
                <?php
                $this->widget('application.modules.reception.components.widgets.OmsFormWidget', array(
                    'form' => $form,
                    'model' => $modelOms
                )); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Редактировать',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/reception/patient/editoms'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
							$("#patient-oms-edit-form").trigger("success", [data, textStatus, jqXHR])
						}'
                    ),
                    array(
                        'class' => 'btn btn-success'
                    )
                ); ?>
            </div>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>
