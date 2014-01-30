<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/chooser.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/calendar.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/writePatient.js" ></script>
<script type="text/javascript">
    globalVariables.months = [
        'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
    ];
</script>
<?php if(Yii::app()->user->checkAccess('writePatient')) { ?>
<div class="row">
    <?php $this->widget('application.modules.reception.components.widgets.WritePatientTabMenu'); ?>
</div>
<h4>Запись опосредованного пациента</h4>
<p class="text-left">
    Шаг 1. Найдите врачей по названному пациентом диагнозу или врачу. Шаг 2. Зарезервируйте время для пациента у этого врача, введя дополнительно контактные данные: ФИО пациента и контактный телефон.
</p>
<h4>Шаг 1. Найти врача</h4>
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
            <p><div class="legend-icon orange-block"></div>Выходные дни</p>
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
</div>
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
                                <input type="text" name="phone" placeholder="Контактный телефон" class="form-control" id="phone">
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