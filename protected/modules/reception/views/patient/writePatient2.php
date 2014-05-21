<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/writePatient.js" ></script>
<?php if($calendarType == 0) { ?>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/calendar.js" ></script>
<?php } elseif($calendarType == 1) { ?>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/organizer.js" ></script>
<?php } ?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<script type="text/javascript">
    globalVariables.cardNumber = '<?php echo $medcard['card_number']; ?>';
    globalVariables.months = [
        'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
    ];
</script>
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
    <?php
    if(isset($greetingId)) {
    ?>
        <script type="text/javascript">
            globalVariables.greetingId = <?php echo $greetingId; ?>;
        </script>
    <?php } ?>
</div>
<h4>
    Необходимо найти врача к которому следует записать пациента <?php echo $oms->last_name.' '.$oms->first_name.' '.$oms->middle_name; ?> :
</h4>
<div class="row">
    <form class="form-horizontal col-xs-12" role="form" id="doctors-search-form">
        <div class = "col-xs-5">
            <div class="form-group">
                <label for="ward" class="col-xs-4 control-label">Отделение</label>
                <div class="col-xs-8">
                    <select class="form-control" id="ward">
                        <?php foreach($wardsList as $id => $name) { ?>
                        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="post" class="col-xs-4 control-label">Должность</label>
                <div class="col-xs-8">
                    <select class="form-control" id="post">
                        <?php foreach($postsList as $id => $name) { ?>
                            <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
			<div class="form-group">
                <label for="post" class="col-xs-4 control-label">Тип приёма</label>
                <div class="col-xs-8">
                    <select class="form-control" id="greetingType">  
						<option value="0">Любой</option>
						<option value="1">Первичный</option>
						<option value="2">Вторичный</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="lastName" class="col-xs-4 control-label">ФИО врача</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" id="fio" placeholder="ФИО врача" <?php echo isset($doctorFio) ? 'value="'.$doctorFio.'"' : ''; ?>>
                </div>
            </div>
            <!--<div class="form-group">
                <label for="lastName" class="col-xs-2 control-label">Фамилия</label>
                <div class="col-xs-4">
                    <input type="text" class="form-control" id="lastName" placeholder="Фамилия">
                </div>
            </div>
            <div class="form-group">
                <label for="firstName" class="col-xs-2 control-label">Имя</label>
                <div class="col-xs-4">
                    <input type="text" class="form-control" id="firstName" placeholder="Имя">
                </div>
            </div>
            <div class="form-group">
                <label for="middleName" class="col-xs-2 control-label">Отчество</label>
                <div class="col-xs-4">
                    <input type="text" class="form-control" id="middleName" placeholder="Отчество">
                </div>
            </div>-->
            <div class="form-group">
                <label for="post" class="col-xs-4 control-label">Дата приёма</label>
                <div class="col-xs-8">
                    <select class="form-control" id="greetingDateComboChoose">
                        <option value="0">Любая</option>
                        <option value="1">Указать конкретную</option>
                    </select>
                </div>
            </div>
            <div class="form-group no-display">
                <label for="greetingDate" class="col-xs-4 control-label required"></label>
                <div id="greetingDate-cont" class="col-xs-5 input-group date">
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
                       <input type="submit" class="btn btn-success" id="doctor-search-submit" value="Найти" />
            </div>
        </div>
        <div class = "col-xs-7">
            <div class="form-group chooser first" id="diagnosisDistribChooser">
                <label for="diagnosis" class="col-xs-4 control-label">Диагноз (enter - добавить)</label>
                <div class="col-xs-6">
                    <input type="text" class="form-control" autofocus id="diagnosis" placeholder="Название диагноза">
                    <ul class="variants no-display">
                    </ul>
                    <div class="choosed">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php if($calendarType == 0) { ?>
    <h4>Необходимо выбрать врача</h4>
    </p>
    <div class="row">
        <div class="col-xs-12 borderedBox">
            <table class="table table-condensed table-hover" id="searchDoctorsResult">
                <thead>
                <tr class="header">
                    <td class="write-patient-cell">
                        Записать
                    </td>
                    <td>
                        ФИО врача
                    </td>
                    <td>
                        Должность
                    </td>
                    <td>
                        Отделение
                    </td>
                    <td>
                        Кабинет
                    </td>
                    <td>
                        Ближайшая дата
                    </td>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="row">
            <ul class="pagination content-pagination">
            </ul>
        </div>
    </div>
            <h4><span class="text-danger busyFio"></span></h4>
    <div class="row">
    <div class="col-xs-5">
                <div class="row busyCalendar no-display">
                        <div class="col-xs-12 ">
                            <div class="headerBusyCalendar no-display">
                             <!--<h4>Занятость <span class="text-danger busyFio"></span> на месяц <span class="text-danger busyDate"></span></h4>-->
                            <h4>Занятость на <span class="text-danger busyDate"></span></h4>
                        </div>
                        <table class="table-bordered table calendar" id="writeShedule">
                            <thead>
                                <tr class="header">
                                    <td>Пн</td>
                                    <td>Вт</td>
                                    <td>Ср</td>
                                    <td>Чт</td>
                                    <td>Пт</td>
                                    <td>Сб</td>
                                    <td>Вс</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="row default-padding-left">
                            <button type="button" class="btn btn-primary" id="showPrevMonth">
                                <span class="glyphicon glyphicon-arrow-left"> </span><span class='prev-months-button'></span>
                            </button>
                            <button type="button" class="btn btn-primary" id="showNextMonth">
                               <span class='next-months-button'></span> <span class="glyphicon glyphicon-arrow-right"></span>
                            </button>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <h4>Легенда</h4>
                        <p><div class="legend-icon orange-block"></div>Выходные дни</p>
                        <p><div class="legend-icon lightgreen-block"></div>Полностью свободные дни</p>
                        <p><div class="legend-icon red-block"></div>Полностью занятые дни</p>
                        <p><div class="legend-icon yellow-block"></div>Частично свободные дни</p>
                        <p><div class="legend-icon not-aviable-block"></div>Прошедшие дни (недоступные для записи)</p>
                    </div>
            </div>
    </div>
    <div class="col-xs-7">
        <div class="busySheduleHeader no-display">
            <h4>Занятость на <span class="text-danger busyDay"></span></h4>
        </div>
        <div class="row busyShedule no-display">
            <div class="col-xs-12 borderedBox">
                <table class="table table-condensed table-hover" id="sheduleByBusy">
                    <thead>
                        <tr class="header">
                            <td class="col-xs-2">Время</td>
                            <td class="col-xs-7">Пациент</td>
                            <td class="col-xs-3 write-patient-cell">Записать</td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php }  elseif($calendarType == 1) { ?>
        <h4>Выберите врача и дату</h4>
        <div class="organizer">
            <div class="sub">
                <div class="headerCont">
                    <table>
                        <tbody>
                        <tr>
                            <td class="week_header_cell doctorH">Врач</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="headerCont2">
                    <table>
                        <tbody>
                        <tr>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="wrap sheduleCont">
                <table class="doctorList">
                </table>
                <ul class="daysListCont">
                </ul>
            </div>
        </div>
    <?php } ?>
<?php } ?>
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
<div class="modal fade error-popup" id="successPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Пациент записан</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p></p>
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
                    <p>По введённым поисковым критериям не найдено ни одного врача.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>