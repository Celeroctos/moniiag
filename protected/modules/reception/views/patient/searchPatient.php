<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/ajaxbutton.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/searchAddPatient.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/motionHistory.js" ></script>
<!--<script type="text/javascript" src="<?php /* echo Yii::app()->request->baseUrl; */?>/js/reception/omsNumber.js" ></script>-->
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<?php if(Yii::app()->user->checkAccess('searchPatient')) { ?>
<h4>Поиск пациента</h4>
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
                <input type="text" class="form-control" autofocus id="omsNumber" placeholder="Номер полиса" data-toggle="tooltip" data-placement="right" title="Номер полиса может состоять из цифр и пробелов">
            </div>
        </div>
        <div class="form-group">
            <label for="cardNumber" class="col-xs-4 control-label">Номер карты</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="cardNumber" placeholder="Номер карты" data-toggle="tooltip" data-placement="right" title="Номер карты вводится в формате номер / год">
            </div>
        </div>
        <div class="form-group">
            <label for="lastName" class="col-xs-4 control-label">Фамилия</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="lastName" placeholder="Фамилия" data-toggle="tooltip" data-placement="right" title="Фамилия может состоять из кириллицы и дефисов (двойные фамилии)">
            </div>
        </div>
        <div class="form-group">
            <label for="firstName" class="col-xs-4 control-label">Имя</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="firstName" placeholder="Имя" data-toggle="tooltip" data-placement="right" title="Имя может состоять из кириллицы и дефисов" >
            </div>
        </div>
        <div class="form-group">
            <label for="middleName" class="col-xs-4 control-label">Отчество</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="middleName" placeholder="Отчество" data-toggle="tooltip" data-placement="right" title="Отчество может состоять из кириллицы и дефисов. Это необязательное поле.">
            </div>
        </div>
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
        <div class="form-group">
            <input type="button" id="patient-search-submit" value="Найти" name="patient-search-submit" class="btn btn-success">
        </div>
    </div>
    <div class="col-xs-7">
        <div class="form-group">
            <label for="serie" class="col-xs-4 control-label">Серия документа</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="serie" placeholder="Серия" data-toggle="tooltip" data-placement="right" title="Серия, номер">
                <input type="text" class="form-control" id="docnumber" placeholder="Номер документа" data-toggle="tooltip" data-placement="right" title="Номер документа может состоять из цифр">
            </div>
        </div>
        <div class="form-group">
            <label for="addressReg" class="col-xs-4 control-label" >Адрес регистрации</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="addressReg" placeholder="Адрес регистрации" data-toggle="tooltip" data-placement="right">
            </div>
        </div>
        <div class="form-group">
            <label for="address" class="col-xs-4 control-label">Адрес прописки</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="address" placeholder="Адрес прописки" data-toggle="tooltip" data-placement="right">
            </div>
        </div>
        <div class="form-group">
            <label for="snils" class="col-xs-4 control-label">СНИЛС</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="snils" placeholder="Формат XXX-XXX-XXX-XX" data-toggle="tooltip" data-placement="right" title="Страховой номер индивидуального лицевого счета гражданина в формате XXX-XXX-XXX-XX, где X - цифра.">
            </div>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>
<div class="row no-display" id="mediateCont">
    <h5>Найденные опосредованные пациенты:</h5>
    <div class="col-xs-6 borderedBox">
        <table class="table table-condensed table-hover" id="omsSearchMediateResult">
            <thead>
            <tr class="header">
                <td></td>
                <td>
                    ФИО
                </td>
                <td>
                    Контактный телефон
                </td>
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
<div class="row no-display" id="withoutCardCont">
    <h5>Найденные пациенты без карт:</h5>
    <div class="col-xs-8 borderedBox">
        <table class="table table-condensed table-hover" id="omsSearchWithoutCardResult">
            <thead>
                <tr class="header">
                    <td></td>
                    <td>
                        ФИО
                    </td>
                    <td>
                        Дата рождения
                    </td>
                    <td>
                        Номер полиса ОМС
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
					<td>
					</td>
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
<div class="row no-display" id="withCardCont">
    <h5>Найденные пациенты с картами:</h5>
    <div class="col-xs-12 borderedBox">
        <table class="table table-condensed table-hover" id="omsSearchWithCardResult">
            <thead>
            <tr class="header">
                <td></td>
                <td>
                    ФИО
                </td>
		<td>
                    Дата рождения
                </td>
                <td>
                    Номер полиса ОМС
                </td>
                <!--<td>
                    Год регистрации карты
                </td>-->
                <td>
                    Номер карты
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
		<td>
                </td>
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
<div class="row no-display" id="mediateSubmit-cont">
    <div class="form-group">
        <input type="button" id="mediate-attach-submit" value="Сопоставить опосредованного пациента с существующими данными" name="mediate-attach-submit" class="btn btn-success disabled">
    </div>
</div>
<?php } ?>
<div class="modal fade error-popup" id="errorSearchPopup">
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
<div class="modal fade error-popup" id="notFoundPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Сообщение</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>По введённым поисковым критериям не найдено ни одного пациента. Вы можете ввести новые данные о пациенте, нажав на кнопку ниже, либо вернуться к поиску.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="createNewPatientBtn">Завести нового пациента</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Вернуться в поиск</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="mediateOkPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Сообщение</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Пациент успешно сопоставлен с существующей картой!</p>
                </div>
            </div>
            <div class="modal-footer">
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
                        'privilegesList' => $privilegesList,
                        'showEditIcon' => 1,
                        'template' => 'application.modules.reception.components.widgets.views.MedcardFormWidget'
                    ));
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/reception/patient/editcard'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                    $("#patient-medcard-edit-form").trigger("success", [data, textStatus, jqXHR])
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
                <?php
                //echo CHtml::submitButton(
                echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/reception/patient/editoms'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                    $("#patient-oms-edit-form").trigger("success", [data, textStatus, jqXHR])
                                }'
                    ),
                    array(
                        'class' => 'btn btn-success',
                        'id' => 'saveOms'
                    )
                ); ?>
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
    'showEditIcon' => 1,
    'template' => 'application.modules.reception.components.widgets.views.addressEditPopup'
));
?>
<div class="modal fade error-popup" id="existOmsPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Полис с таким номером уже существует</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <span class = 'concidesOmsDataMessage no-display'>
                        <p>ФИО: <strong><span id="fioExistingOms"></span></strong></p>
                        <p>Дата рождения: <strong><span id="birthdayExistingOms"></span></strong></p>
                        <p class="oldCardOnNewOmsMessage"></p>
                        <p>Использовать этот полис для данной медкарты?</p>
                    </span>
                    <!-- Сообщение о том, что неплохо было перепроверить данные по ОМС -->
                    <!--<p class="red-color nonCoidenceOmsMessage no-display"><strong>Внимание!
                        Данные старого полиса для этой карты и полиса, номер которого вы ввели при редактировании не совпадают!
                        Пожалуйста, перепроверьте ФИО пациента и номер карты. Если всё правильно - нажмите "Да" или "Нет" - чтобы проверить.
                    </strong></p>
                     -->
                    <span class="red-color nonConcidesOmsDataMessage no-display">
                        <strong>
                            <p>Вы хотите присвоить номер полиса, принадлежащий</p>
                            <p>ФИО: <strong><span id="fioNewOms"></span></strong></p>
                            <p>Дата рождения: <strong><span id="birthdayNewOms"></span></strong></p>
                            <p class="newCardOmsMessage"></p>
                            <p>пациенту:</p>
                            <p>ФИО: <strong><span id="fioOldOms"></span></strong></p>
                            <p>Дата рождения: <strong><span id="birthdayOldOms"></span></strong></p>
                            <p class="oldCardOmsMessage"></p>
                            <p>Присвоить полис другому лицу?</p>
                        </strong>
                    </span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Да</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Нет</button>
            </div>
        </div>
    </div>
</div>
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
<div class="modal fade error-popup" id="foundPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Сообщение</h4>
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