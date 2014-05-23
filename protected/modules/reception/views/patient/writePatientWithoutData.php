<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js" ></script>
<?php if($calendarType == 0) { ?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/calendar.js" ></script>
<?php } elseif($calendarType == 1) { ?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/organizer.js" ></script>
<?php } ?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/writePatient.js" ></script>
<script type="text/javascript">
    globalVariables.months = [
        'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
    ];
    globalVariables.calendarType = <?php echo $calendarType; ?>;
</script>
<?php if(Yii::app()->user->checkAccess('writePatient')) { ?>
<div class="row">
    <?php $this->widget('application.modules.reception.components.widgets.WritePatientTabMenu',
        array(
            'callcenter' => $callcenter
        )
    ); ?>
    <script type="text/javascript">
        globalVariables.isCallCenter = <?php echo $callcenter; ?>;
    </script>
    <?php
    if(isset($greetingId)) {
        ?>
        <script type="text/javascript">
            globalVariables.greetingId = <?php echo $greetingId; ?>;
        </script>
    <?php } ?>
</div>
<h4>Необходимо найти врача к которому следует записать пациента на приём:</h4>
<div class="row">
    <form class="form-horizontal col-xs-12" role="form" id="doctors-search-form">
        <div class="col-xs-5">
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
            <div class="form-group">
                <label for="canPregnant" class="col-xs-4 control-label">Беременная</label>
                <div class="col-xs-8">
                    <select class="form-control" id="canPregnant">
                        <option value="0">Нет</option>
                        <option value="1">Да</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success" id="doctor-search-submit" value="Найти" />
            </div>
        </div>
        <div class="col-xs-7">
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
</div>
<?php } elseif($calendarType == 1) { ?>
<h4>Выберите врача и дату</h4>
<div class="row organizerNav no-display">
    <button class="btn btn-primary back">
        <span class="glyphicon glyphicon-arrow-left"></span>Раньше
    </button>
    <button class="btn btn-primary forward">
        Позже<span class="glyphicon glyphicon-arrow-right"></span>
    </button>
</div>
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
<div class="modal fade error-popup" id="patientDataPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="#" id="enterprise-edit-form" role="form" class="form-horizontal col-xs-12">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Введите данные о пациенте для резервирования времени</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label for="lastName" class="col-xs-3 control-label required">Фамилия <span class="required">*</span></label>
                            <div class="col-xs-9">
                                <input type="text" name="lastName" placeholder="Фамилия" class="form-control" id="lastName">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="firstName" class="col-xs-3 control-label required">Имя <span class="required">*</span></label>
                            <div class="col-xs-9">
                                <input type="text" name="firstName" placeholder="Имя" class="form-control" id="firstName">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="middleName" class="col-xs-3 control-label required">Отчество</label>
                            <div class="col-xs-9">
                                <input type="text" name="middleName" placeholder="Отчество" class="form-control" id="middleName">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="col-xs-3 control-label required">Контактный телефон <span class="required">*</span></label>
                            <div class="col-xs-9">
                                <input type="text" name="phone" placeholder="Контактный телефон" class="form-control" id="phone" value="+7">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment" class="col-xs-3 control-label">Комментарий</label>
                            <div class="col-xs-9">
                                <textarea name="comment" placeholder="Комментарий" class="form-control" id="comment" value=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="submitReservData">Записать данные</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </form>
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
<? } ?>
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