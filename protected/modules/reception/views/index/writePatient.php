<h4>Запись пациента</h4>
<p class="text-left">
    Шаг 1. Найдите пациента с помощью формы ниже. Шаг 2. Выберите, к какому врачу записать пациента и на какое время, нажав на иконку часов в строке таблицы рядом с пациентом.
</p>
<h4>Шаг 1. Найти пациента</h4>
<p class="text-left">
    Задайте условия поиска
</p>
<div class="row">
    <form class="form-horizontal col-xs-9" role="form">
        <div class="form-group">
            <label for="enterprise" class="col-xs-3 control-label">Учреждение</label>
            <div class="col-xs-9">
                <select class="form-control" id="enterprise">
                    <option>Поликлиника #1</option>
                    <option>Поликлиника #2</option>
                    <option>Поликлиника #3</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="medcardNumber" class="col-xs-3 control-label">Номер карты</label>
            <div class="col-xs-9">
                <input type="text" class="form-control" id="medcardNumber" placeholder="Номер карты">
            </div>
        </div>
        <div class="form-group">
            <label for="policy" class="col-xs-3 control-label">Полис</label>
            <div class="col-xs-9">
                <input type="text" class="form-control" id="policy" placeholder="Номер полиса">
            </div>
        </div>
        <div class="form-group">
            <label for="lastName" class="col-xs-3 control-label">Фамилия</label>
            <div class="col-xs-9">
                <input type="text" class="form-control" id="lastName" placeholder="Фамилия">
            </div>
        </div>
        <div class="form-group">
            <label for="firstName" class="col-xs-3 control-label">Имя</label>
            <div class="col-xs-9">
                <input type="text" class="form-control" id="firstName" placeholder="Имя">
            </div>
        </div>
        <div class="form-group">
            <label for="middleName" class="col-xs-3 control-label">Отчество</label>
            <div class="col-xs-9">
                <input type="text" class="form-control" id="middleName" placeholder="Отчество">
            </div>
        </div>
        <div class="form-group">
            <div class="search-shedule-submit">
                <button type="submit" class="btn btn-success col-md-offset-2">Найти</button>
            </div>
        </div>
    </form>
</div>
<h4>Список пациентов на запрос <span class="text-danger">"Болейк"</span></h4>
<p class="text-left">
    В таблице отображаются результаты поискового запроса.
</p>
<div class="row">
    <div class="col-xs-12 borderedBox">
        <table class="table table-condensed table-hover" id="sheduleByHours">
            <thead>
            <tr class="header">
                <td>
                    ФИО пациента
                </td>
                <td>
                    Номер карты
                </td>
                <td>
                    Год регистрации
                </td>
                <td>
                    Редактировать
                </td>
                <td>
                    Записать на приём
                </td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <a href="#" title="Посмотреть данные по больному">
                        Болейко П. Т.
                    </a>
                </td>
                <td>
                    <a href="#" title="Посмотреть историю карты">
                        1134/87
                    </a>
                </td>
                <td>
                    2012
                </td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                </td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="#" title="Посмотреть данные по больному">
                        Болейкоc П. Т.
                    </a>
                </td>
                <td>
                    <a href="#" title="Посмотреть историю карты">
                        1134/88
                    </a>
                </td>
                <td>
                    2011
                </td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                </td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="#" title="Посмотреть данные по больному">
                        Болейка П. Т.
                    </a>
                </td>
                <td>
                    <a href="#" title="Посмотреть историю карты">
                        1134/89
                    </a>
                </td>
                <td>
                    2011
                </td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                </td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="#" title="Посмотреть данные по больному">
                        Болейкин П. Т.
                    </a>
                </td>
                <td>
                    <a href="#" title="Посмотреть историю карты">
                        1134/83
                    </a>
                </td>
                <td>
                    2012
                </td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                </td>
                <td>
                    <a href="#">
                        <span class="glyphicon glyphicon-dashboard"></span>
                    </a>
                </td>
            </tr>
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
</div>
