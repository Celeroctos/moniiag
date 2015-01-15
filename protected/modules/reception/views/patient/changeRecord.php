<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/ajaxbutton.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/changegreetings.js" ></script>
<div class="row">
    <?php $this->widget('application.modules.reception.components.widgets.WritePatientTabMenu',
        array(
            'callcenter' => $callcenter
        )
    ); ?>
</div>
<script type="text/javascript">
    globalVariables.isCallCenter = <?php echo $callcenter; ?>;
</script>
<h4>
    Изменение или отмена записей
</h4>
<div class="row">
    <form class="form-horizontal col-xs-12" role="form" id="doctors-search-greetings">
        <div class="form-group">
            <label for="doctorFio" class="col-xs-2 control-label">ФИО врача</label>
            <div class="col-xs-4">
                <input type="text" class="form-control" id="doctorFio" placeholder="ФИО врача">
            </div>
        </div>
        <div class="form-group">
            <label for="patientFio" class="col-xs-2 control-label">ФИО пациента</label>
            <div class="col-xs-4">
                <input type="text" class="form-control" id="patientFio" placeholder="ФИО пациента">
            </div>
        </div>
        <div class="form-group">
            <label for="greetingDate" class="col-xs-2 control-label required">Дата приёма</label>
            <div id="greetingDate-cont" class="col-xs-3 input-group date">
                <input type="hidden" name="birthday" placeholder="Формат гггг-мм-дд" class="form-control col-xs-4" id="greetingDate">
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
            <label for="cardNumber" class="col-xs-2 control-label">Номер карты</label>
            <div class="col-xs-4">
                <input type="text" class="form-control" id="cardNumber" placeholder="Номер карты" title="Номер карты вводится в формате номер / год">
            </div>
        </div>

        <div class="form-group">
            <label for="phoneFilter" class="col-xs-2 control-label">Номер телефона</label>
            <div class="col-xs-4">
                <input type="text" class="form-control" id="phoneFilter" placeholder="Номер телефона" title="Номер телефона">
            </div>
        </div>
        <div class="form-group">
            <input type="button" id="greetings-search-submit" value="Найти" name="greetings-search-submit" class="btn btn-success">
        </div>
    </form>
    <div class="col-xs-12 borderedBox">
        <table class="table table-condensed table-hover" id="greetingsSearchResult">
            <thead>
            <tr class="header">
                <td>
                    Номер карты
                </td>
                <td>
                    ФИО пациента
                </td>
                <td>
                    Дата приёма
                </td>
                <td>
                    Время приёма
                </td>
                <td>
                    ФИО врача, специальность
                </td>
                <td>
                    Контактный телефон
                </td>
                <td class="cancelGreetingTh">
                    <!-- Отменить приём -->
                </td>
                <td class="editGreetingTh">
                    <!-- Изменить приём -->
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
<div class="modal fade error-popup" id="notFoundPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Сообщение</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>По введённым поисковым критериям не найдено ни одного пациента, либо задан пустой поисковой запрос.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Вернуться в поиск</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="cannotUnwritePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Сообщение</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>