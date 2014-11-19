<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/daterangepicker/moment.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/daterangepicker/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/daterangepicker/daterangepicker-bs3.css" />
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/sheduleEditing/editorBehavior.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/sheduleEditing/commonBehavior.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/sheduleEditing/editorInitsializing.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/sheduleEditing/ports/EditorToJSON.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/sheduleEditing/ports/JSONToEditor.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/sheduleEditing/ports/JSONToPreview.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/chooser.js" ></script>
<!-- тег center - это конечно плохо, но пока пусть будет -->
<center><h4>Графики работы персонала</h4></center>
<!-- Выводим распределение докторов по отделениям -->
<script>
    globalVariables.doctorsForWards = <?php echo CJSON::encode($doctorsForWards); ?>
</script>
<script>
    globalVariables.factsForSelect = <?php echo CJSON::encode($factsForJSON); ?>
</script>
<!-- Выбрасываем кабинеты с номерами и всеми причиндалами-->
<script>
    globalVariables.cabinetsArray = <?php echo CJSON::encode($cabinetsListIds); ?>
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
<div class="addingNewSheduleContainer row no-display">
    <button type="button" class="addingNewShedule btn btn-default" data-dismiss="modal">Составить расписание</button>
</div>
<div id="edititngSheduleArea" class="no-display">

</div>
<div id="existingSheduleArea" class="no-display">
    <!-- Контейнер для пагинации -->


    <!-- Спискота готовых графиков -->
    <div class="col-xs-12" id="existingSheduleAreaList">



    </div>
    <!-- Контейнер для пагинации -->
    <div class="row pagination-container">
        <ul class="pagination content-pagination">
        </ul>
    </div>
</div>
<!-- Блок, содержащий шаблоны для вывода графиков в разных режимах (редактирования, просмотра и пр) -->
<!-- JavaScript берёт отсюда блоки, заполняет их значениями для каждого из графиков(правил)
    и выводит в интерфейс заполненными -->
<div class="no-display" id="timetableTemplates">
    <!-- Шаблон вывода графика в режиме просмотра-->
    <div class="col-xs-12 timetableReadOnly">
        <input class="timeTableId" type="hidden">
        <input class="timeTableJSON" type="hidden">
        <div class="col-xs-12 timeTableRODateTimesAction">
           <strong><nobr>C <span class="timeTableROFrom"></span> по <span class="timeTableROTo"></span></nobr></strong>
        </div>


        <table class="timeTablesRODoctorsWards">
            <tr>
                <td class = "wardsColEditing">
                    <strong>Отделения:</strong>
                </td>
                <td class = "doctorsColEditing">
                    <strong>Врачи:</strong>
                </td>
            </tr>
            <tr class="oneRowDoctorsWardRO">
                <td class = "wardsColRO">

                </td>
                <td class = "doctorsColRO">

                </td>
            </tr>
        </table>


        <table class="timeTablesROTable">
            <thead>
                <tr>
                    <td class="roomTD">
                        Кабинет
                    </td>
                    <td class="daysTD">
                        Дни работы
                    </td>
                    <td class="hoursOfWorkTD no-display">
                        Часы работы
                    </td>
                    <td class="hoursOfGreetingTD">
                        Часы приёма
                    </td>
                    <td class="shiftTD no-display">
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
                    <td class="hoursOfWorkTD no-display">
                    </td>
                    <td class="hoursOfGreetingTD">
                    </td>
                    <td class="shiftTD no-display">
                    </td>
                    <td class="limitTD">
                    </td>
                    <td class="factsTD">
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Кнопки "Изменить и "Удалить"-->
        <div style="display:inline-block;">
            <button type="button" class="changeSheduleButton btn btn-default" data-dismiss="modal">Изменить</button>
            <button type="button" class="deleteSheduleButton btn btn-default" data-dismiss="modal">Удалить</button>
            <button type="button" class="addAddingSheduleButton btn btn-default" data-dismiss="modal">Добавить</button>
        </div>
    </div>

    <div class="col-xs-12" id="timetableEditing">
        <!-- Шаблон для редактирования графика -->
        <input class="timeTableId" type="hidden">
        <div class="col-xs-12 timeTableEditDateTimesAction no-display">
            <strong><nobr>C <span class="timeTableEditFrom"></span> по <span class="timeTableEditTo"></span></nobr></strong>
        </div>
        <table class="timeTablesEditDoctorsWards">
            <tr class="">
                <td class = "wardsColEditing">
                    <strong>Отделения:</strong>
                </td>
                <td class = "doctorsColEditing">
                    <strong>Врачи:</strong>
                </td>
            </tr>
                <tr class="oneRowDoctorsWardEditing">
                    <td class = "wardsColEditing">

                    </td>
                    <td class = "doctorsColEditing">

                    </td>
                </tr>
        </table>
        <!-- -->
        <!--div class="col-xs-12 timeTablesEditDoctorsWards">
            <div class="col-xs-6 timeTablesEditWards">
                <div class="col-xs-4 timeTablesEditWardsLabel">Отделение:</div>
                <div class="col-xs-8 timeTablesEditWardsValue">
                    Анатомии ресниц<br>Офтальмологическое<br>Болезней лёгких курильщиков
                </div>
            </div>
            <div class="col-xs-6 timeTablesEditDoctors">
                Рабинович Евграф Аристархович<br>Джонсон аль-Мухаммед аль-Ибрагим<br>Моисеев Борис
            </div>
        </div-->
        <!-- -->
        <table class="timeTablesEditTable">
            <thead>
            <tr class="withBorderTDTimetableEditor">
                <td class="roomTD">
                    Кабинет
                </td>
                <td class="daysTD">
                    Дни работы
                </td>
                <td class="hoursOfWorkTD no-display">
                    Часы работы
                </td>
                <td class="hoursOfGreetingTD">
                    Часы приёма
                </td>
                <td class="shiftTD no-display">
                    Смена
                </td>
                <td class="limitTD">
                    Лимит на приём
                </td>
                <td class="factsTD">
                    Обстоятельство
                </td>
                <td class="deleteTD">
                </td>
            </tr>
            </thead>
            <tbody>
            <tr class="withBorderTDTimetableEditor oneRowRuleTimetable">
                <td class="roomTD">
                    <!-- селект с кабинетами -->
                    <select class="cabinetSelectEdit">
                        <?php
                       // var_dump($cabinetsList);
                       // exit();
                        foreach ($cabinetsList as $oneCabinet)
                        {
                            ?>
                              <option value="<?php echo $oneCabinet['id']; ?>"><?php
                                  echo ($oneCabinet['cab_number'].' - '.$oneCabinet['description']);
                                  ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td class="daysTD">
                    <!-- Совершенно жуткая ячейка с набором разных параметров -->
                    <!-- Верхний блок - дни недели и номер недели в месяце -->
                    <div class="daysEditingTopBLock">
                        <table class="daysOfWeekEdit" >
                            <tr>
                                <td><nobr><input class="weekDay1" type = "checkbox">Пн</nobr></td>
                                <td><nobr><input class="weekDay2" type = "checkbox">Вт</nobr></td>
                                <td><nobr><input class="weekDay3" type = "checkbox">Ср</nobr></td>
                                <td><nobr><input class="weekDay4" type = "checkbox">Чт</nobr></td>
                                <td><nobr><input class="weekDay5" type = "checkbox">Пт</nobr></td>
                                <td><nobr><input class="weekDay6" type = "checkbox">Сб</nobr></td>
                                <td><nobr><input class="weekDay7" type = "checkbox">Вс</nobr></td>
                            </tr>
                            <tr>
                                <td>
                                    <nobr><input type="checkbox" name="weekDayNumber1_1" />1ый</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber1_2" />2ой</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber1_3" />3ий</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber1_4" />4ый</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber1_5" />5ый</nobr><br />
                                </td>
                                <td>
                                    <nobr><input type="checkbox" name="weekDayNumber2_1" />1ый</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber2_2" />2ой</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber2_3" />3ий</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber2_4" />4ый</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber2_5" />5ый</nobr><br />
                                </td>
                                <td>

                                    <nobr><input type="checkbox" name="weekDayNumber3_1" />1ая</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber3_2" />2ая</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber3_3" />3я</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber3_4" />4ая</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber3_5" />5ая</nobr><br />
                                </td>
                                <td>

                                    <nobr><input type="checkbox" name="weekDayNumber4_1" />1ый</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber4_2" />2ой</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber4_3" />3ий</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber4_4" />4ый</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber4_5" />5ый</nobr><br />
                                </td>
                                <td>

                                    <nobr><input type="checkbox" name="weekDayNumber5_1" />1ая</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber5_2" />2ая</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber5_3" />3я</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber5_4" />4ая</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber5_5" />5ая</nobr><br />
                                </td>
                                <td>

                                    <nobr><input type="checkbox" name="weekDayNumber6_1" />1ая</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber6_2" />2ая</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber6_3" />3я</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber6_4" />4ая</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber6_5" />5ая</nobr><br />
                                </td>
                                <td>

                                    <nobr><input type="checkbox" name="weekDayNumber7_1" />1ое</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber7_2" />2ое</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber7_3" />3е</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber7_4" />4ое</nobr><br />
                                    <nobr><input type="checkbox" name="weekDayNumber7_5" />5ое</nobr><br />
                                </td>
                            </tr>

                        </table>
                    </div>
                    <!-- Нижний блок - Даты и чётность/нечётность -->
                    <table class ="daysEditingBottomBLock" >
                        <tr>
                        <td class="calendarBlock">
                            <div class="input-group date date-timetable null-padding-left">
                                <input class="form-control" placeholder="" title="" style="width: 200px;" name="addDateTimetable" type="hidden" value="">
                                <span class="input-group-addon">
                                            <span class="glyphicon-calendar glyphicon">
                                            </span>
                                </span>
                            </div>
                            <div class= "calendarBlockLabel">
                            Выбрать дату(ы) в<br>календаре
                            </div>
                        </td>
                        <td class="exceptionBlock">
                            <!-- Чекбокс чётности-нечётности -->
                            <div class="oddCheckbox">
                                <div style="display:inline-block;">
                                    <input type="checkbox" name="oddDays">Чётные
                                </div>
                                <div style="display:inline-block;">
                                    <input type="checkbox" name="notoddDays">Нечетные
                                </div>
                            </div>
                            <!-- Селект исключений -->
                            <div class="exceptionSelectContainer">
                                Кроме:<select class="exceptionSelect" multiple="multiple">
                                    <!--option value="-2">Даты в календаре</option-->
                                    <option value="1">Понедельника</option>
                                    <option value="2">Вторника</option>
                                    <option value="3">Среды</option>
                                    <option value="4">Четверга</option>
                                    <option value="5">Пятницы</option>
                                    <option value="6">Субботы</option>
                                    <option value="7">Воскресенья</option>
                                </select>
                            </div>
                        </td>
                        </tr>
                    </table>
                    <div class="daysEditingDatesBlock">
                    </div>
                </td>
                <td class="hoursOfWorkTD no-display">
                    <div class="workingHourBlock">
                        <b>С</b>
                        <div>
                            <div class="input-group date time-control time-timetable workingHourBeginTime">
                                <input type="hidden" class="form-control">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                <div class="subcontrol">
                                    <div class="form-inline subfields">
                                        <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                        <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--br -->
                        <b>По</b>
                        <div>

                            <div class="input-group date time-control time-timetable workingHourEndTime">
                                <input type="hidden" class="form-control">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                <div class="subcontrol">
                                    <div class="form-inline subfields">
                                        <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                        <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                    </div>
                                </div>
                            </div>

                        </div>
                    <div>
                </td>
                <td class="hoursOfGreetingTD">
                    <div class="greetingHourBlock">
                        <b>С</b>
                        <div>
                            <div class="input-group date time-control time-timetable greetingHourBeginTime">
                                <input type="hidden" class="form-control">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                <div class="subcontrol">
                                    <div class="form-inline subfields">
                                        <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                        <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <b>По</b>
                        <div>
                            <div class="input-group date time-control time-timetable greetingHourEndTime">
                                <input type="hidden" class="form-control">
                                <span class="input-group-addon no-display">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                <div class="subcontrol">
                                    <div class="form-inline subfields">
                                        <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                        <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div></td>
                <td class="shiftTD no-display">
                    <!-- Пока забиваем -->
                </td>
                <td class="limitTD">
                    <!-- Селект и три блока -->
                    <select class="sourceSelect">
                        <option value="1">Call-Центр</option>
                        <option value="2">Регистратура</option>
                        <option value="3">Интернет</option>
                    </select>
                    <div class="limitBlock limitBlock1">
                        Количество<br>
                        <input type="text" class = "limitQuantity limitQuantity1">
                        <br>Время<br>
                        <div class="input-group date time-control time-timetable limitTime1">
                            <input type="hidden" class="form-control">
                            <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                            </span>
                            <div class="subcontrol">
                                <div class="form-inline subfields">
                                    <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                    <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                </div>
                            </div>
                        </div>
                        <div class="input-group date time-control time-timetable limitTime1End">
                            <input type="hidden" class="form-control">
                             <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                            </span>
                            <div class="subcontrol">
                                <div class="form-inline subfields">
                                    <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                    <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="limitBlock limitBlock2">
                        Количество<br>
                        <input type="text" class = "limitQuantity limitQuantity2">
                        <br>Время<br>
                        <div class="input-group date time-control time-timetable limitTime2">
                            <input type="hidden" class="form-control">
                             <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                            </span>
                            <div class="subcontrol">
                                <div class="form-inline subfields">
                                    <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                    <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                </div>
                            </div>
                        </div>
                        <div class="input-group date time-control time-timetable limitTime2End">
                            <input type="hidden" class="form-control">
                             <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                            </span>
                            <div class="subcontrol">
                                <div class="form-inline subfields">
                                    <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                    <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="limitBlock limitBlock3">
                        Количество<br>
                        <input type="text" class = "limitQuantity limitQuantity3">
                        <br>Время<br>
                        <div class="input-group date time-control time-timetable limitTime3">
                            <input type="hidden" class="form-control">
                             <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                            </span>
                            <div class="subcontrol">
                                <div class="form-inline subfields">
                                    <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                    <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                </div>

                            </div>
                        </div>
                        <div class="input-group date time-control time-timetable limitTime3End">
                            <input type="hidden" class="form-control">
                             <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                            </span>
                            <div class="subcontrol">
                                <div class="form-inline subfields">
                                    <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                    <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                </div>
                            </div>
                        </div>
                    </div>

                </td>
                <td class="factsTD">
                    <select class="factsSelect">
                        <option value="-1"></option>
                        <?php
                            foreach ($factsForSelect as $oneFact)
                            {
                                ?>
                                    <option value="<?php echo $oneFact['id']; ?>"><?php echo $oneFact['name']; ?></option>
                                <?php
                            }
                        ?>
                    </select>
                    <!-- Чекбокс чётности-нечётности -->
                    <div class="rangeCheckbox no-display">
                            <input type="checkbox" name="rangeFact">Период
                            <br>
                            <input type="checkbox" name="notRangeFact">Один день
                    </div>
                    <div class="factsOneDateCalendarContainer no-display">
                        <div class="input-group date date-timetable null-padding-left addFactDateTimetable-cont">
                            <input class="form-control" placeholder="" title="" style="width: 200px;" name="addFactDateTimetable" type="hidden" value="">
                                <span class="input-group-addon">
                                            <span class="glyphicon-calendar glyphicon">
                                            </span>
                                </span>
                        </div>
                    </div>
                    <div class="factsRangeDateCalendarContainer no-display">
                        <div class="input-group date date-timetable null-padding-left addFactRangeTimetable-cont">
                            <input type="hidden" name="factRangeBegin">
                            <input type="hidden" name="factRangeEnd">
                                <span class="input-group-addon factsRangeButton">
                                            <span class="glyphicon-calendar glyphicon">
                                            </span>
                                </span>
                        </div>
                    </div>
                    <div class="factsDatesBlock">
                    </div>
                </td>
                <td class="deleteTD">
                    <span class="removeTimeTableRule glyphicon glyphicon-remove-circle" title="Удалить"></span>
                </td>
            </tr>
            <tr class="addRuleButtons">
            <!-- Строка с кнопками добавить -->
                <td class="roomTD">
                    <button type="button" class="addingNewSheduleRoom btn btn-default" data-dismiss="modal">Добавить<br>правило</button>
                </td>
                <td class="daysTD no-display">
                    <button type="button" class="addingNewSheduleDays btn btn-default" data-dismiss="modal">Добавить дни</button>
                </td>
                <td class="hoursOfWorkTD no-display">
                    <button type="button" class="addingNewSheduleHourWork btn btn-default" data-dismiss="modal">Добавить<br>часы</button>
                </td>
                <td class="hoursOfGreetingTD no-display">
                    <button type="button" class="addingNewSheduleHourGreeting btn btn-default" data-dismiss="modal">Добавить<br>часы</button>
                </td>
                <td class="shiftTD no-display">
                    Смена
                </td>
                <td class="limitTD no-display">
                    <button type="button" class="addingNewSheduleLimit btn btn-default" data-dismiss="modal">Добавить лимит</button>
                </td>
                <td class="factsTD no-display">

                </td>
                <td class="deleteTD no-display">

                </td>
            </tr>
            </tbody>
        </table>
        <div class="sheduleEditorFooter">
            <span class="lifetimeOfTimetable">Укажите период действия графика</span>
            <div class="timeTableBegin">

                <div class="form-group">
                  <label>C</label><br>
                  <div class="col-xs-3 input-group date date-control sheduleBeginDateTime-cont">
                        <input type="hidden" class="sheduleBeginDateTime form-control col-xs-4" placeholder="Формат гггг-мм-дд" name="reportDate">
				        <span class="input-group-addon">
					        <span class="glyphicon-calendar glyphicon">
					        </span>
				        </span>
                        <div class="subcontrol">
                            <div class="form-inline subfields">
                                <input type="text" class="form-control day" placeholder="ДД" name="day">
                                <input type="text" class="form-control month" placeholder="ММ" name="month">
                                <input type="text" class="form-control year" placeholder="ГГГГ" name="year">
                            </div>
                        </div>
                    </div>
                </div>



            </div>
            <div class="timeTableEnd">
                <div class="form-group">
                    <label>По</label><br>
                    <div class="col-xs-3 input-group date date-control sheduleEndDateTime-cont">
                        <input type="hidden" class="form-control col-xs-4 sheduleEndDateTime" placeholder="Формат гггг-мм-дд" name="reportDate">
				        <span class="input-group-addon">
					        <span class="glyphicon-calendar glyphicon">
					        </span>
				        </span>
                        <div class="subcontrol">
                            <div class="form-inline subfields">
                                <input type="text" class="form-control day" placeholder="ДД" name="day">
                                <input type="text" class="form-control month" placeholder="ММ" name="month">
                                <input type="text" class="form-control year" placeholder="ГГГГ" name="year">
                            </div>
                        </div>
                    </div>
                </div>



            </div>
            <div class="timetableEditorHandlingButtons">
                <br>
                <button type="button" class="saveSheduleButton btn btn-default" data-dismiss="modal">Сохранить</button>
                <button type="button" class="cancelSheduleButton btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
    <!-- Контейнер для хранения латы в ячейке "Дни работы" -->
    <div class="daysOneDateContainer">
        <span class="daysOneDateValue"></span>
        <span class="glyphicon glyphicon-remove daysOneDateValueRemove"></span>
    </div>
    <!-- Контейнер для хранения обстоятельств (дежурство, неприёмный день и т.п.) -->
    <div class="factsItemContainer">
        <input type="hidden" class="typeFactVal no-display">
        <input type="hidden" class="isRange no-display">
        <input type="hidden" class="dateFactBegin no-display">
        <input type="hidden" class="dateFactEnd no-display">
        <nobr><span class="glyphicon glyphicon-remove factRemoveButton"></span>
        <span class="factTextCaptionReason"></span></nobr>
        <br><span class="factTextCaptionDate"></span>
    </div>
    <!-- Контейнер для хранение пределов приёма при выводе существующего расписания-->
    <div class="limitROBlock">
        <span class="limitObjectName"></span><br>
        <span class="limitROBody"></span>
    </div>
    <div class="oneFactROBlock">
        <table>
            <tr>
                <td class="oneFactTypeRO">
                </td>
                <td class="oneFactTimeRO">
                </td>
            </tr>
        </table>
    </div>

</div>

<div class="modal fade error-popup" id="deleteTemplatePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <!--<h4 class="modal-title">Успешно!</h4>-->
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Вы действительно хотите удалить расписание?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id = "timetableButtonDelete" data-dismiss="modal">Удалить</button>
                <button type="button" class="btn btn-close" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade error-popup" id="onDeleteTimetablePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="row cancelledGreetingsMessage no-display">
                    <p>Количество приёмов, отменённых при удалении расписания: <span class="cancelledGreetingsNumber"></span><br>Их необходимо перезаписать на другое время</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade error-popup" id="successEditPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Успешно!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Расписание успешно сохранено</p>
                </div>
                <div class="row cancelledGreetingsMessage no-display">
                    <p>Количество отменённых приёмов: <span class="cancelledGreetingsNumber"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
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