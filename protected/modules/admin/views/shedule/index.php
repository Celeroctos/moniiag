<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/shedule.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/chooser.js" ></script>
<!-- тег center - это конечно плохо, но пока пусть будет -->
<center><h4>Графики работы персонала</h4></center>
<!-- Выводим распределение докторов по отделениям -->
<script>
    globalVariables.doctorsForWards = <?php echo CJSON::encode($doctorsForWards); ?>
</script>

<div class="row">
    <div class="col-xs-6">
        <div class="doctorsWardsBlock">
            <span class="timetableDoctorsHead"><h5>Отделение</h5></span>
            <!-- Список отделений -->
            <div class="form-group">
                <div class="col-xs-12">
                    <select class="form-control" id="wardSelect" multiple="multiple">
                        <?php foreach($wardsList as $id => $name) { ?>
                            <option value="<?php echo $id; ?>" <?php  echo (  ($id==-1)?'selected':''   ); ?>><?php echo $name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>


        </div>
    </div>

    <div class="col-xs-6">
        <div class="doctorsWardsBlock">
            <span class="timetableDoctorsHead"><h5>Сотрудник</h5></span>
            <!-- Спискота всех сотрудников -->
            <div class="form-group">
                <div class="col-xs-12">
                    <select class="form-control" id="doctorsSelect" multiple="multiple">
                        <?php foreach($doctorList as $oneDoctor) { ?>
                            <option value="<?php echo $oneDoctor['id']; ?>"><?php echo $oneDoctor['fio']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Блок, содержащий шаблоны для вывода графиков в разных режимах (редактирования, просмотра и пр) -->
<!-- JavaScript берёт отсюда блоки, заполняет их значениями для каждого из графиков(правил)
    и выводит в интерфейс заполненными -->
<div class="nodisplay" id="#timetableTemplates">
    <!-- Шаблон вывода графика в режиме просмотра-->
    <div class="col-xs-12" id="timetableReadOnly">
        <span class="timeTableId no-display"></span>
        <span class="timeTableJSON no-display"></span>
        <div class="col-xs-12 timeTableRODateTimesAction">
           <nobr>C <span class="timeTableROFrom"></span> по <span class="timeTableROTo"></span></nobr>
        </div>
        <div class="col-xs-12 timeTablesRODoctorsWards">
            <div class="col-xs-6 timeTablesROWards">
                <div class="col-xs-4 timeTablesROWardsLabel">Отделение:</div>
                <div class="col-xs-8 timeTablesROWardsValue">
                    Анатомии ресниц<br>Офтальмологическое<br>Болезней лёгких курильщиков
                </div>
            </div>
            <div class="col-xs-6 timeTablesRODoctors">
                    Рабинович Евграф Аристархович<br>Джонсон аль-Мухаммед аль-Ибрагим<br>Моисеев Борис
            </div>
        </div>
        <table class="timeTablesROTable">
            <thead>
                <tr>
                    <td class="roomTD">
                        Кабинет
                    </td>
                    <td class="daysTD">
                        Дни работы
                    </td>
                    <td class="hoursOfWorkTD">
                        Часы работы
                    </td>
                    <td class="hoursOfGreetingTD">
                        Часы приёма
                    </td>
                    <td class="shiftTD">
                        Смена
                    </td>
                    <td class="limitTD">
                        Лимит на приём
                    </td>
                    <td class="factsTD">
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="roomTD">
                    </td>
                    <td class="daysTD">
                    </td>
                    <td class="hoursOfWorkTD">
                    </td>
                    <td class="hoursOfGreetingTD">
                    </td>
                    <td class="shiftTD">
                    </td>
                    <td class="limitTD">
                    </td>
                    <td class="factsTD">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="col-xs-12" id="#timetableEditing">

    </div>

</div>