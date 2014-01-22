<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/chooser.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/writePatient.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/calendar.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<script type="text/javascript">
    globalVariables.cardNumber = '<?php echo $medcard['card_number']; ?>';
    globalVariables.months = [
        'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
    ];
</script>
<?php if(Yii::app()->user->checkAccess('writePatient')) { ?>
<h4>Запись пациента</h4>
<p class="text-left">
    Шаг 1. Найдите пациента с помощью формы ниже. Шаг 2. Выберите, к какому врачу записать пациента и на какое время, нажав на иконку часов в строке таблицы рядом с пациентом.
</p>
<h4>Шаг 2. Найти врача и назначить время</h4>
<p class="text-left">
    Задайте условия поиска. В результаты попадут записи, подходящие минимум по двум выбранным критериям, если не поставлен флажок для точного поиска
</p>
<div class="row">
    <form class="form-horizontal col-xs-12" role="form" id="doctors-search-form">
        <div class="form-group">
            <label for="ward" class="col-xs-2 control-label">Отделение</label>
            <div class="col-xs-4">
                <select class="form-control" id="ward">
                    <?php foreach($wardsList as $id => $name) { ?>
                    <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="post" class="col-xs-2 control-label">Должность</label>
            <div class="col-xs-4">
                <select class="form-control" id="post">
                    <?php foreach($postsList as $id => $name) { ?>
                        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="lastName" class="col-xs-2 control-label">ФИО врача</label>
            <div class="col-xs-4">
                <input type="text" class="form-control" id="fio" placeholder="ФИО врача">
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
        <div class="form-group chooser first" id="diagnosisDistribChooser">
            <label for="diagnosis" class="col-xs-2 control-label">Диагноз (enter - добавить)</label>
            <div class="col-xs-4">
                <input type="text" class="form-control" autofocus id="diagnosis" placeholder="Название диагноза">
                <ul class="variants no-display">
                </ul>
                <div class="choosed">
                </div>
            </div>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-success" id="doctor-search-submit" value="Найти" />
        </div>
    </form>
</div>
<h4>Список врачей по поисковому запросу</h4>
<p class="text-left">
    В таблице отображаются результаты поискового запроса.
</p>
<div class="row">
    <div class="col-xs-12 borderedBox">
        <table class="table table-condensed table-hover" id="searchDoctorsResult">
            <thead>
            <tr class="header">
                <td>
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
                    Ближайшая свободная дата
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
<div class="headerBusyCalendar no-display">
    <h4>Занятость врача <span class="text-danger busyFio"></span> на месяц <span class="text-danger busyDate"></span></h4>
    <p class="text-left">
        Кликните на дату левой кнопкой мыши, чтобы посмотреть почасовую занятость врача на этот день
    </p>
    
</div>
<div class="row busyCalendar no-display">
    <div class="col-xs-8 ">
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
                <span class="glyphicon glyphicon-arrow-left"></span> Показать предыдущий месяц
            </button>
            <button type="button" class="btn btn-primary" id="showNextMonth">
               Показать следующий месяц <span class="glyphicon glyphicon-arrow-right"></span>
            </button>
        </div>
    </div>
    <div class="col-xs-4">
        <h4>Легенда</h4>
        <p><div class="legend-icon lightgreen-block"></div>Полностью свободные дни</p>
        <p><div class="legend-icon red-block"></div>Полностью занятые дни</p>
        <p><div class="legend-icon yellow-block"></div>Частично свободные дни</p>
        <p><div class="legend-icon not-aviable-block"></div>Прошедшие дни (недоступные для записи)</p>
    </div>
</div>
<div class="busySheduleHeader no-display">
    <h4>Занятость врача <span class="text-danger busyFio"></span> на <span class="text-danger busyDay"></span></h4>
    <p class="text-left">
        Кликните на иконку часов левой кнопкой мыши, чтобы записать пациента на это время
    </p>
</div>
<div class="row busyShedule no-display">
    <div class="col-xs-8 borderedBox">
        <table class="table table-condensed table-hover" id="sheduleByBusy">
            <thead>
                <tr class="header">
                    <td>Время</td>
                    <td>Пациент</td>
                    <td>Записать на приём / отписать с приёма</td>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
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
                <h4 class="modal-title">Успешно!</h4>
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