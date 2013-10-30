<h4>Добавление пациента</h4>
<p class="text-left">
    Не нашли в списке пациентов нужного? Добавьте запись о нём, заполнив поля формы.
</p>
<div class="row default-padding">
    <form class="form-horizontal col-xs-9" role="form">
        <div class="row">
            <div class="col-xs-6">
                <div class="form-group">
                    <label for="policy" class="col-xs-3 control-label">Номер полиса</label>
                    <div class="col-xs-5">
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
                    <label for="gender" class="col-xs-3 control-label">Пол</label>
                    <div class="col-xs-4">
                        <select class="form-control" id="gender">
                            <option>Женский</option>
                            <option>Мужской</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="birthday" class="col-xs-3 control-label">Дата рождения</label>
                    <div class="col-xs-9 input-group date" id="birthday-cont">
                        <input type="text" class="form-control" id="birthday" placeholder="Дата рождения" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="snils" class="col-xs-3 control-label">СНИЛС</label>
                    <div class="col-xs-5">
                        <input type="text" class="form-control" id="snils" placeholder="СНИЛС">
                    </div>
                </div>
                <div class="form-group">
                    <label for="addressReg" class="col-xs-3 control-label">Адрес регистрации</label>
                    <div class="col-xs-9">
                        <input type="text" class="form-control" id="addressReg" placeholder="Адрес регистрации">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address" class="col-xs-3 control-label">Адрес проживания</label>
                    <div class="col-xs-9">
                        <input type="text" class="form-control" id="address" placeholder="Адрес проживания">
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <label for="doctype" class="col-xs-3 control-label">Тип документа</label>
                    <div class="col-xs-9">
                        <select class="form-control" id="doctype">
                            <option>Паспорт РФ</option>
                            <option>Вид на жительство</option>
                            <option>Военный билет</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="serie" class="col-xs-3 control-label">Серия</label>
                    <div class="col-xs-3">
                        <input type="text" class="form-control" id="serie" placeholder="Серия">
                    </div>
                </div>
                <div class="form-group">
                    <label for="docnumber" class="col-xs-3 control-label">Номер</label>
                    <div class="col-xs-6">
                        <input type="text" class="form-control" id="docnumber" placeholder="Номер">
                    </div>
                </div>
                <div class="form-group">
                    <label for="whoGived" class="col-xs-3 control-label">Кем выдан</label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="whoGived" placeholder="Кем выдан">
                    </div>
                </div>
                <div class="form-group">
                    <label for="birthday" class="col-xs-3 control-label">Дата выдачи</label>
                    <div class="col-xs-9 input-group date" id="document-givedate-cont">
                        <input type="text" class="form-control" id="document-givedate-cont" placeholder="Дата выдачи" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="privilege" class="col-xs-3 control-label">Тип льготы</label>
                    <div class="col-xs-9">
                        <select class="form-control" id="privilege">
                            <option>Нет льготы</option>
                            <option>Бесплатное обслуживание</option>
                            <option>Скидка на таблетки</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="invalidGroup" class="col-xs-3 control-label">Группа инвалидности</label>
                    <div class="col-xs-9">
                        <select class="form-control" id="invalidGroup">
                            <option>Нет</option>
                            <option>I</option>
                            <option>II</option>
                            <option>III</option>
                            <option>IV</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="add-patient-submit ">
                <button type="submit" class="btn btn-success">Добавить</button>
            </div>
        </div>
    </form>
</div>