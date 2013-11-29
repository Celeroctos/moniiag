<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/writePatient.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
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
            <td>
                Записать
            </td>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="row">
    <ul class="pagination content-pagination">
        <li><a href="#">&laquo;</a></li>
        <li class="active"><a href="#">1</a></li>
        <li><a href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#">4</a></li>
        <li><a href="#">5</a></li>
        <li><a href="#">&raquo;</a></li>
    </ul>
</div>
<h4>Занятость врача <span class="text-danger">Иванов А. К.</span> на месяц <span class="text-danger">Сентябрь 2013 г.</span></h4>
<p class="text-left">
    Кликните на дату левой кнопкой мыши, чтобы посмотреть почасовую занятость врача на этот день
</p>
<div class="row">
    <div class="col-xs-8 ">
        <table class="table-bordered table" id="sheduleByDays">
            <tr>
                <td class="text-muted">30</td>
                <td class="text-muted">1</td>
                <td class="text-muted">2</td>
                <td class="text-muted">3</td>
                <td class="text-muted">4</td>
                <td class="text-muted">5</td>
                <td class="text-muted">6</td>
            </tr>
            <tr>
                <td class="text-muted">7</td>
                <td class="text-muted">8</td>
                <td class="text-muted">9</td>
                <td class="text-muted">10</td>
                <td class="text-muted">11</td>
                <td class="text-muted">12</td>
                <td class="text-muted">13</td>
            </tr>
            <tr>
                <td class="text-muted">14</td>
                <td class="text-muted">15</td>
                <td class="text-muted">16</td>
                <td class="text-muted">17</td>
                <td class="text-muted">18</td>
                <td class="text-muted">19</td>
                <td class="text-muted">20</td>
            </tr>
            <tr>
                <td class="yellow-block">21</td>
                <td class="yellow-block">22</td>
                <td class="red-block">23</td>
                <td class="red-block">24</td>
                <td class="red-block">25</td>
                <td class="lightgreen-block">26</td>
                <td>27</td>
            </tr>
            <tr>
                <td class="lightgreen-block">28</td>
                <td class="lightgreen-block">29</td>
                <td class="lightgreen-block">30</td>
                <td class="lightgreen-block">31</td>
                <td class="text-muted">1</td>
                <td class="text-muted">2</td>
                <td class="text-muted">3</td>
            </tr>
        </table>
    </div>
    <div class="col-xs-4">
        <h4>Легенда</h4>
        <p><div class="legend-icon lightgreen-block"></div>Полностью свободные дни</p>
        <p><div class="legend-icon red-block"></div>Полностью занятые дни</p>
        <p><div class="legend-icon yellow-block"></div>Частично свободные дни</p>
    </div>
</div>
<h4>Занятость врача <span class="text-danger">Иванов А. К.</span> на <span class="text-danger">21.09.2013 г.</span></h4>
<p class="text-left">
    Кликните на иконку часов левой кнопкой мыши, чтобы записать пациента на это время
</p>
<div class="row">
    <div class="col-xs-8 borderedBox">
        <table class="table table-condensed table-hover" id="sheduleByBusy">
            <thead>
            <tr class="header">
                <td>Время</td>
                <td>Пациент</td>
                <td>Записать на приём</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>9.00 - 9.30</td>
                <td></td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
            <tr>
                <td>9.30 - 10.00</td>
                <td></td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>10.00 - 10.30</td>
                <td>
                    <a href="#" title="Посмотреть информацию по пациенту">
                        Неминущий А. С.
                    </a>
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>10.30 - 11.00</td>
                <td></td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>11.00 - 11.30</td>
                <td></td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>11.30 - 12.00</td>
                <td></td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>12.00 - 12.30</td>
                <td>
                    <a href="#" title="Посмотреть информацию по пациенту">
                        Тараканов Т. С.
                    </a>
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>12.30 - 13.00</td>
                <td></td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>13.00 - 13.30</td>
                <td>
                    <a href="#" title="Посмотреть информацию по пациенту">
                        Копейкин М. А.
                    </a>
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>13.30 - 14.00</td>
                <td></td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>14.00 - 14.30</td>
                <td></td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>14.30 - 15.00</td>
                <td></td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>15.00 - 15.30</td>
                <td></td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>15.30 - 16.00</td>
                <td></td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>16.00 - 16.30</td>
                <td></td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>16.30 - 17.00</td>
                <td></td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>