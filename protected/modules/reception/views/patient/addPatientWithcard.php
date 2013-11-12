 <h4>Добавление карты к существующему пациенту</h4>
    <p class="text-left">
        Заполните поля формы, чтобы добавить карту к существующему пациенту <span class="text-danger bold">(<?php echo $fio; ?>, полис №<?php echo $policy_number; ?>)</span>
    </p>
<div class="row default-padding">
    <form class="form-horizontal col-xs-9" role="form">
        <div class="row">
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
                    <label for="workPlace" class="col-xs-3 control-label">Место работы</label>
                    <div class="col-xs-9">
                        <input type="text" class="form-control" id="workPlace" placeholder="Место работы">
                    </div>
                </div>
                <div class="form-group">
                    <label for="workAddress" class="col-xs-3 control-label">Адрес работы</label>
                    <div class="col-xs-9">
                        <input type="text" class="form-control" id="workAddress" placeholder="Адрес работы">
                    </div>
                </div>
                <div class="form-group">
                    <label for="post" class="col-xs-3 control-label">Должность</label>
                    <div class="col-xs-9">
                        <input type="text" class="form-control" id="post" placeholder="Должность">
                    </div>
                </div>
                <div class="form-group">
                    <label for="contact" class="col-xs-3 control-label">Контактные данные</label>
                    <div class="col-xs-9">
                        <input type="text" class="form-control" id="contact" placeholder="Контактные данные">
                    </div>
                </div>
                <div class="form-group">
                    <label for="snils" class="col-xs-3 control-label">СНИЛС</label>
                    <div class="col-xs-5">
                        <input type="text" class="form-control" id="snils" placeholder="СНИЛС">
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