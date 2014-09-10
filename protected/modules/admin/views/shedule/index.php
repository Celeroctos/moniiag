<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/shedule.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/chooser.js" ></script>
<h4>Графики работы персонала</h4>
<!-- Выводим распределение докторов по отделениям -->
<script>
    globalVariables.doctorsForWards = <?php echo CJSON::encode($doctorsForWards); ?>
</script>

<div class="row">
    <div class="col-xs-6">
        <div>
            Отделение
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
        <div>
            Сотрудник
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

<!-- Дальше код отключаем -->
<?php if (false){

    ?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/writtenPatients.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/shedule.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<h4>Редактирование рабочего времени врачей</h4>
<p>Раздел предлагает возможность задания рабочего графика врачей. Найдите врача с помощью поискового фильтра и задайте ему расписание.</p>
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
    В таблице отображаются результаты поискового запроса. Кликните на ФИО врача, чтобы ему установить расписание.
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
                    Специальность
                </td>
                <td>
                    Отделение
                </td>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="row no-display">
	<ul class="pagination content-pagination">
	</ul>
    </div>
</div>

<div id="sheduleEditCont" class="no-display">
    <!--<div class="row">-->
        <h5><strong>Параметры расписания</strong></h5>

		    <div id="sheduleShiftsEmployee">
                <h4>Список смен для сотрудника</h4>
                <p></p>
                <table id="shiftsEmployee"></table>
                <div id="shiftsEmployeePager"></div>
				<div class="btn-group default-margin-top">
					<button type="button" class="btn btn-default" id="addSheduleEmployee">Добавить расписание</button>
					<button type="button" class="btn btn-default" id="editSheduleEmployee">Редактировать расписание</button>
					<button type="button" class="btn btn-default" id="deleteSheduleEmployee">Удалить расписание</button>
				</div>
            </div>
    <!--</div>-->
	<div class="row col-xs-12">
	<h5><strong>Дни-исключения</strong></h5>
    <p>Если у врача есть дни с особым раписанием, занесите их сюда. Для удаления дня оставьте <strong>все текстовые поля</strong> строки пустыми.</p>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'shedule-exp-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/shedule/addeditexps'),
        'htmlOptions' => array(
            'class' => 'form-horizontal',
            'role' => 'form'
        )
    ));
    ?>
    

        <div class="borderedBox shedule">
            <table class="col-xs-12 table table-condensed table-hover" id="shedule-exp-table">
                <thead>
                    <tr class="header">
                        <td>
                            День
                        </td>
                        <td>
                            С
                        </td>
                        <td>
                            По
                        </td>
                        <td>
                            Кабинет
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($daysExp as $i => $item) { ?>
                    <tr>
                        <?php
                        echo $form->hiddenField($item, "[$i]id", array(
                            'id' => 'id'.$i,
                            'class' => 'form-control'
                        ));
                        echo $form->hiddenField($item, "[$i]doctorId", array(
                            'id' => 'doctorId'.$i,
                            'class' => 'form-control'
                        ));
                        ?>
                        <td class="col-xs-3">
                            <div class="col-xs-4 input-group date" id="day0-cont">
                            <?php echo $form->hiddenField($item,"[$i]day", array(
                                            'id' => 'day'.$i,
                                            'class' => 'form-control',
                                            'placeholder' => 'Формат гггг-мм-дд'
                                )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
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
                        </td>
                        <td class="col-xs-3">
                            <div class="input-group date time-control" id="timeBegin-cont0">
                                    <?php echo $form->hiddenField($item,"[$i]timeBegin", array(
                                        'id' => 'timeBegin'.$i,
                                        'class' => 'form-control'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                <div class="subcontrol">
                                    <div class="time-ctrl-up-buttons">
                                        <div class="btn-group">
                                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                        </div>
                                    </div>
                                    <div class="form-inline subfields">
                                        <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                        <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                    </div>
                                    <div class="time-ctrl-down-buttons">
                                        <div class="btn-group">
                                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="col-xs-3">
                            <div class="input-group date time-control" id="timeEnd-cont0">
                                <?php echo $form->hiddenField($item,"[$i]timeEnd", array(
                                    'id' => 'timeEnd'.$i,
                                    'class' => 'form-control'
                                )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                <div class="subcontrol">
                                    <div class="time-ctrl-up-buttons">
                                        <div class="btn-group">
                                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                        </div>
                                    </div>
                                    <div class="form-inline subfields">
                                        <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                        <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                    </div>
                                    <div class="time-ctrl-down-buttons">
                                        <div class="btn-group">
                                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="col-xs-3">
                            <?php echo $form->dropDownList($item,"[$i]cabinet", $cabinetList, array(
                                'id' => 'cabinet'.$i,
                                'class' => 'form-control'
                            )); ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row default-padding">
        <div class="form-group default-padding-left">
            <?php echo CHtml::ajaxSubmitButton(
                'Сохранить',
                CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/shedule/addeditexps'),
                array(
                    'success' => 'function(data, textStatus, jqXHR) {
                        $("#shedule-exp-form").trigger("success", [data, textStatus, jqXHR])
                    }'
                ),
                array(
                    'class' => 'btn btn-success',
                    'id' => 'doctor-exp-submit'
                )
            ); ?>
            <input type="button" value="Добавить ещё день-исключение" id="doctor-exp-add" class="btn btn-success">
        </div>
    </div>
    <?php $this->endWidget(); ?>
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
<div class="modal fade error-popup" id="errorAddShedulePopup">
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
<div class="modal fade error-popup" id="errorEditShedulePopup">
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
                    <p>Вы успешно отредактировали расписание.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="successAddEmployeeShedule">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Успешно!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Вы успешно добавили расписание для сотрудника</p>
                    <p id = "messageRewritePatients" class = "no-display">Количество пациентов, которых необходимо перезаписать на другое время: <span id = "numberPatientsToRewrite"></span>. Нажмите <a href="/index.php/reception/patient/viewrewrite">сюда</a>, чтобы посмотреть их список</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="successDeleteEmployeeShedule">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Успешно!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Вы успешно удалили расписание для сотрудника</p>
                    <p id = "messageRewritePatients" class = "no-display">Количество пациентов, которых необходимо перезаписать на другое время: <span id = "numberPatientsToRewrite"></span>. Нажмите <a href="/index.php/reception/patient/viewrewrite">сюда</a>, чтобы посмотреть их список</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade error-popup" id="successEditEmployeeShedule">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Успешно!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Вы успешно отредактировали расписание для сотрудника.</p>
                    <p id = "messageRewritePatients" class = "no-display">Количество пациентов, которых необходимо перезаписать на другое время: <span id = "numberPatientsToRewrite"></span>. Нажмите <a href="/index.php/reception/patient/viewrewrite">сюда</a>, чтобы посмотреть их список</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="editShedulePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать расписание для сотрудника</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Вы успешно отредактировали расписание.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade error-popup" id="addShedulePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить расписание для сотрудника</h4>
            </div>
			 <?php
			 $form = $this->beginWidget('CActiveForm', array(
			 	'id' => 'add-shedule-employee',
			 	'enableAjaxValidation' => true,
			 	'enableClientValidation' => true,
			 	'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/shedule/addedit'),
			 	'htmlOptions' => array(
			 				'class' => 'form-horizontal col-xs-12',
			 				'role' => 'form'
			 				)
			 ));?>
			
            <div class="modal-body">
			       <?
			        echo $form->hiddenField($model,'doctorId', array(
			        	'id' => 'doctorId',
			        	'class' => 'form-control'
			        	));
			       echo $form->hiddenField($model,'sheduleEmployeeId', array(
			       	'id' => 'sheduleEmployeeId',
			       	'class' => 'form-control'
			       	));
					
					  echo $form->hiddenField($model,'weekEnds', array(
			        	'id' => 'weekEnds',
			        	'class' => 'form-control'
			        	));
			        ?>
			<div class="radio">
                <label>
                    <input type="radio" name="sheduleType" value="0" checked="ckeched" />Стандартное
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="sheduleType" value="1" />Индивидуальное
                </label>
            </div>
            <div id="sheduleShifts" class="row">
                <h4>Список смен</h4>
                <p>Кликните на смену в таблице два раза, чтобы установить её в качестве раписания.</p>
                <table id="shiftsAdd"></table>
                <div id="shiftsPagerAdd"></div>
            </div>
            <div class="form-group default-margin-top">
                <?php echo $form->labelEx($model,'dateBegin', array(
                	'class' => 'col-xs-3 control-label requared'
                )); ?>
                <div class="col-xs-4 input-group date" id="shift-date-begin-cont">
                    <?php echo $form->hiddenField($model,'dateBegin', array(
                    	'id' => 'dateBegin',
                    	'class' => 'form-control',
                    	'placeholder' => 'Формат гггг-мм-дд'
                    )); ?>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
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
                <?php echo $form->labelEx($model,'dateEnd', array(
                	'class' => 'col-xs-3 control-label requared'
                )); ?>
                <div class="col-xs-4 input-group date" id="shift-date-end-cont">
                    <?php echo $form->hiddenField($model,'dateEnd', array(
                    	'id' => 'dateEnd',
                    	'class' => 'form-control',
                    	'placeholder' => 'Формат гггг-мм-дд'
                    )); ?>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
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
			
            
            <h5><strong>Стабильное расписание</strong></h5>
            <p>Для занесения дня в нерабочие оставьте <strong>поля времени</strong> пустыми.</p>
            <div class="borderedBox default-margin-top col-xs-12 shedule">
                <table class="col-xs-12 table table-condensed table-hover" id ="addingTimeTable">
                    <thead>
                        <tr class="header">
                            <td class="col-xs-1">
                                День
                            </td>
                            <td class="col-xs-3">
                                С
                            </td>
                            <td class="col-xs-3">
                                По
                            </td>
                            <td class="col-xs-5">
                                Кабинет
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Пн</strong>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeBegin1-cont">
                                    <?php echo $form->hiddenField($model,'timeBegin1', array(
                                    	'id' => 'timeBegin1',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd1-cont">
                                    <?php echo $form->hiddenField($model,'timeEnd1', array(
                                    	'id' => 'timeEnd1',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet1', $cabinetList, array(
                                	'id' => 'cabinet1',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Вт</strong>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeBegin2-cont">
                                    <?php echo $form->hiddenField($model,'timeBegin2', array(
                                    	'id' => 'timeBegin2',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd2-cont">
                                    <?php echo $form->hiddenField($model,'timeEnd2', array(
                                    	'id' => 'timeEnd2',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet2', $cabinetList, array(
                                	'id' => 'cabinet2',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Ср</strong>
                            </td>
                            <td class="col-xs-3">
                                 <div class="col-xs-9 input-group date time-control" id="timeBegin3-cont">
                                    <?php echo $form->hiddenField($model,'timeBegin3', array(
                                    	'id' => 'timeBegin3',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd3-cont">
                                    <?php echo $form->hiddenField($model,'timeEnd3', array(
                                    	'id' => 'timeEnd3',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet3', $cabinetList, array(
                                	'id' => 'cabinet3',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Чт</strong>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeBegin4-cont">
                                        <?php echo $form->hiddenField($model,'timeBegin4', array(
                                        	'id' => 'timeBegin4',
                                        	'class' => 'form-control',
                                        	'placeholder' => 'Формат (чч:мм)'
                                        )); ?>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                        <div class="subcontrol">
                                            <div class="time-ctrl-up-buttons">
                                                <div class="btn-group">
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                                </div>
                                            </div>
                                            <div class="form-inline subfields">
                                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                            </div>
                                            <div class="time-ctrl-down-buttons">
                                                <div class="btn-group">
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd4-cont">
                                    <?php echo $form->hiddenField($model,'timeEnd4', array(
                                    	'id' => 'timeEnd4',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet4', $cabinetList, array(
                                	'id' => 'cabinet4',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Пт</strong>
                            </td>
                            <td class="col-xs-3">
                        <div class="col-xs-9 input-group date time-control" id="timeBegin5-cont">
                        <?php echo $form->hiddenField($model,'timeBegin5', array(
                        	'id' => 'timeBegin5',
                        	'class' => 'form-control',
                        	'placeholder' => 'Формат (чч:мм)'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="time-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                            </div>
                            <div class="time-ctrl-down-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                                
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd5-cont">
                        <?php echo $form->hiddenField($model,'timeEnd5', array(
                        	'id' => 'timeEnd5',
                        	'class' => 'form-control',
                        	'placeholder' => 'Формат (чч:мм)'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="time-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                            </div>
                            <div class="time-ctrl-down-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                                
                                
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet5', $cabinetList, array(
                                	'id' => 'cabinet5',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Сб</strong>
                            </td>
                            <td class="col-xs-3">
                        <div class="col-xs-9 input-group date time-control" id="timeBegin6-cont">
                        <?php echo $form->hiddenField($model,'timeBegin6', array(
                        	'id' => 'timeBegin6',
                        	'class' => 'form-control',
                        	'placeholder' => 'Формат (чч:мм)'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="time-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                            </div>
                            <div class="time-ctrl-down-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                                
                                
                                
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd6-cont">
                        <?php echo $form->hiddenField($model,'timeEnd6', array(
                        	'id' => 'timeEnd6',
                        	'class' => 'form-control',
                        	'placeholder' => 'Формат (чч:мм)'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="time-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                            </div>
                            <div class="time-ctrl-down-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                                
                                
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet6', $cabinetList, array(
                                	'id' => 'cabinet6',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Вс</strong>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeBegin0-cont">
                                    <?php echo $form->hiddenField($model,'timeBegin0', array(
                                    	'id' => 'timeBegin0',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd0-cont">
                                    <?php echo $form->hiddenField($model,'timeEnd0', array(
                                    	'id' => 'timeEnd0',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet0', $cabinetList, array(
                                	'id' => 'cabinet0',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
			  <div class="row default-padding x2-default-padding-left">
                <div class="form-group">
                   
                </div>
            </div>
           
				<!--<p>======================</p>-->
           
        </div>
        <div class="modal-footer">
		 <?php echo CHtml::ajaxSubmitButton(
		 	'Сохранить',
		 	CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/shedule/addedit'),
		 	array(
		 			'success' => 'function(data, textStatus, jqXHR) {
                                $("#add-shedule-employee").trigger("success", [data, textStatus, jqXHR])
                            }'
		 			),
		 		array(
		 			'class' => 'btn btn-success',
		 			'id' => 'doctor-shedule-add-submit'
		 			)
		 ); ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
		<?php $this->endWidget(); ?>
        </div>
    </div>
</div>



<div class="modal fade error-popup" id="editSheduleEmployeePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать расписание для сотрудника</h4>
            </div>
			 <?php
			 $form = $this->beginWidget('CActiveForm', array(
			 	'id' => 'edit-shedule-employee',
			 	'enableAjaxValidation' => true,
			 	'enableClientValidation' => true,
			 	'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/shedule/addedit'),
			 	'htmlOptions' => array(
			 				'class' => 'form-horizontal col-xs-12',
			 				'role' => 'form'
			 				)
			 ));?>
			
            <div class="modal-body">
			       <?
			       echo $form->hiddenField($model,'doctorId', array(
			       	'id' => 'doctorId',
			       	'class' => 'form-control'
			       	));
			       echo $form->hiddenField($model,'sheduleEmployeeId', array(
			       	'id' => 'sheduleEmployeeId',
			       	'class' => 'form-control'
			       	));
			       
			       echo $form->hiddenField($model,'weekEnds', array(
			       	'id' => 'weekEnds',
			       	'class' => 'form-control'
			       	));
			       ?>
			<div class="radio">
                <label>
                    <input type="radio" name="sheduleType" value="0" checked="ckeched" />Стандартное
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="sheduleType" value="1" />Индивидуальное
                </label>
            </div>
            <div id="sheduleShifts" class="row">
                <h4>Список смен</h4>
                <p>Кликните на смену в таблице два раза, чтобы установить её в качестве раписания.</p>
                <table id="shiftsEdit"></table>
                <div id="shiftsPagerEdit"></div>
            </div>
            <div class="form-group default-margin-top">
                <?php echo $form->labelEx($model,'dateBegin', array(
                	'class' => 'col-xs-3 control-label requared'
                )); ?>
                <div class="col-xs-4 input-group date" id="shift-date-begin-cont">
                    <?php echo $form->hiddenField($model,'dateBegin', array(
                    	'id' => 'dateBegin',
                    	'class' => 'form-control',
                    	'placeholder' => 'Формат гггг-мм-дд'
                    )); ?>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
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
                <?php echo $form->labelEx($model,'dateEnd', array(
                	'class' => 'col-xs-3 control-label requared'
                )); ?>
                <div class="col-xs-4 input-group date" id="shift-date-end-cont">
                    <?php echo $form->hiddenField($model,'dateEnd', array(
                    	'id' => 'dateEnd',
                    	'class' => 'form-control',
                    	'placeholder' => 'Формат гггг-мм-дд'
                    )); ?>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
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
			
            
            <h5><strong>Стабильное расписание</strong></h5>
            <p>Для занесения дня в нерабочие оставьте <strong>поля времени</strong> пустыми.</p>
            <div class="borderedBox default-margin-top col-xs-12 shedule">
                <table class="col-xs-12 table table-condensed table-hover" id ="editingTimeTable">
                    <thead>
                        <tr class="header">
                            <td class="col-xs-1">
                                День
                            </td>
                            <td class="col-xs-3">
                                С
                            </td>
                            <td class="col-xs-3">
                                По
                            </td>
                            <td class="col-xs-5">
                                Кабинет
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Пн</strong>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeBegin1-cont">
                                    <?php echo $form->hiddenField($model,'timeBegin1', array(
                                    	'id' => 'timeBegin1',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd1-cont">
                                    <?php echo $form->hiddenField($model,'timeEnd1', array(
                                    	'id' => 'timeEnd1',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet1', $cabinetList, array(
                                	'id' => 'cabinet1',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Вт</strong>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeBegin2-cont">
                                    <?php echo $form->hiddenField($model,'timeBegin2', array(
                                    	'id' => 'timeBegin2',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd2-cont">
                                    <?php echo $form->hiddenField($model,'timeEnd2', array(
                                    	'id' => 'timeEnd2',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet2', $cabinetList, array(
                                	'id' => 'cabinet2',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Ср</strong>
                            </td>
                            <td class="col-xs-3">
                                 <div class="col-xs-9 input-group date time-control" id="timeBegin3-cont">
                                    <?php echo $form->hiddenField($model,'timeBegin3', array(
                                    	'id' => 'timeBegin3',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd3-cont">
                                    <?php echo $form->hiddenField($model,'timeEnd3', array(
                                    	'id' => 'timeEnd3',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet3', $cabinetList, array(
                                	'id' => 'cabinet3',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Чт</strong>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeBegin4-cont">
                                        <?php echo $form->hiddenField($model,'timeBegin4', array(
                                        	'id' => 'timeBegin4',
                                        	'class' => 'form-control',
                                        	'placeholder' => 'Формат (чч:мм)'
                                        )); ?>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                        <div class="subcontrol">
                                            <div class="time-ctrl-up-buttons">
                                                <div class="btn-group">
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                                </div>
                                            </div>
                                            <div class="form-inline subfields">
                                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                            </div>
                                            <div class="time-ctrl-down-buttons">
                                                <div class="btn-group">
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd4-cont">
                                    <?php echo $form->hiddenField($model,'timeEnd4', array(
                                    	'id' => 'timeEnd4',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet4', $cabinetList, array(
                                	'id' => 'cabinet4',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Пт</strong>
                            </td>
                            <td class="col-xs-3">
                        <div class="col-xs-9 input-group date time-control" id="timeBegin5-cont">
                        <?php echo $form->hiddenField($model,'timeBegin5', array(
                        	'id' => 'timeBegin5',
                        	'class' => 'form-control',
                        	'placeholder' => 'Формат (чч:мм)'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="time-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                            </div>
                            <div class="time-ctrl-down-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                                
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd5-cont">
                        <?php echo $form->hiddenField($model,'timeEnd5', array(
                        	'id' => 'timeEnd5',
                        	'class' => 'form-control',
                        	'placeholder' => 'Формат (чч:мм)'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="time-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                            </div>
                            <div class="time-ctrl-down-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                                
                                
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet5', $cabinetList, array(
                                	'id' => 'cabinet5',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Сб</strong>
                            </td>
                            <td class="col-xs-3">
                        <div class="col-xs-9 input-group date time-control" id="timeBegin6-cont">
                        <?php echo $form->hiddenField($model,'timeBegin6', array(
                        	'id' => 'timeBegin6',
                        	'class' => 'form-control',
                        	'placeholder' => 'Формат (чч:мм)'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="time-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                            </div>
                            <div class="time-ctrl-down-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                                
                                
                                
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd6-cont">
                        <?php echo $form->hiddenField($model,'timeEnd6', array(
                        	'id' => 'timeEnd6',
                        	'class' => 'form-control',
                        	'placeholder' => 'Формат (чч:мм)'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="time-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                            </div>
                            <div class="time-ctrl-down-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                                
                                
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet6', $cabinetList, array(
                                	'id' => 'cabinet6',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-xs-1">
                                <strong>Вс</strong>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeBegin0-cont">
                                    <?php echo $form->hiddenField($model,'timeBegin0', array(
                                    	'id' => 'timeBegin0',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-3">
                                <div class="col-xs-9 input-group date time-control" id="timeEnd0-cont">
                                    <?php echo $form->hiddenField($model,'timeEnd0', array(
                                    	'id' => 'timeEnd0',
                                    	'class' => 'form-control',
                                    	'placeholder' => 'Формат (чч:мм)'
                                    )); ?>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                    <div class="subcontrol">
                                        <div class="time-ctrl-up-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon minute-button up-minute-button"></button>
                                            </div>
                                        </div>
                                        <div class="form-inline subfields">
                                            <input type="text" name="hour" placeholder="ЧЧ" class="form-control hour">
                                            <input type="text" name="minute" placeholder="ММ" class="form-control minute">
                                        </div>
                                        <div class="time-ctrl-down-buttons">
                                            <div class="btn-group">
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-hour-button"></button>
                                                <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon minute-button down-minute-button"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-xs-5">
                                <?php echo $form->dropDownList($model,'cabinet0', $cabinetList, array(
                                	'id' => 'cabinet0',
                                	'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
			  <div class="row default-padding x2-default-padding-left">
                <div class="form-group">
                   
                </div>
            </div>
           
				<!--<p>======================</p>-->
           
        </div>
        <div class="modal-footer">
		 <?php echo CHtml::ajaxSubmitButton(
		 	'Сохранить',
		 	CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/shedule/addedit'),
		 	array(
		 			'success' => 'function(data, textStatus, jqXHR) {
                                $("#edit-shedule-employee").trigger("success", [data, textStatus, jqXHR])
                            }'
		 			),
		 		array(
		 			'class' => 'btn btn-success',
		 			'id' => 'doctor-shedule-edit-submit'
		 			)
		 ); ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
		<?php $this->endWidget(); ?>
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
<div class="modal fade error-popup" id="viewWritedPatient">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                 <h4 class="modal-title">Для врача на эти даты уже записаны пациенты по старому расписанию</h4>
                 <p>Пожалуйста, отпишите данных пацентов у этого врача прежде, чем расписание будет изменено</p>
             </div>
             <div class="modal-body">
                 <div id="writtenPatientsTimetable" class="row">
                     <table id="writtenPatients"></table>
                     <div id="writtenPatientsPager"></div>
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
                <h4 class="modal-title">Ошибка</h4>
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
</div><?php } ?>