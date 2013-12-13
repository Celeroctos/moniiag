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
                    Должность
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
</div>
<div id="sheduleEditCont" class="no-display">
    <div class="row">
        <h5><strong>Параметры расписания</strong></h5>
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'shedule-by-day-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/shedule/addedit'),
            'htmlOptions' => array(
                'class' => 'form-horizontal col-xs-12',
                'role' => 'form'
            )
        ));
            echo $form->hiddenField($model,'doctorId', array(
                'id' => 'doctorId',
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
                <table id="shifts"></table>
                <div id="shiftsPager"></div>
            </div>
            <div class="form-group default-margin-top">
                <?php echo $form->labelEx($model,'dateBegin', array(
                    'class' => 'col-xs-3 control-label required'
                )); ?>
                <div id="dateBegin-cont" class="col-xs-4 input-group date">
                    <?php echo $form->textField($model,'dateBegin', array(
                        'id' => 'dateBegin',
                        'class' => 'form-control',
                        'placeholder' => 'Формат гггг-мм-дд'
                    )); ?>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'dateEnd', array(
                    'class' => 'col-xs-3 control-label required'
                )); ?>
                <div id="dateEnd-cont" class="col-xs-4 input-group date">
                    <?php echo $form->textField($model,'dateEnd', array(
                        'id' => 'dateEnd',
                        'class' => 'form-control',
                        'placeholder' => 'Формат гггг-мм-дд'
                    )); ?>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <h5><strong>Стабильное расписание</strong></h5>
            <p>Для занесения дня в нерабочие оставьте <strong>поля времени</strong> пустыми.</p>
            <div class="borderedBox default-margin-top col-xs-12 shedule">
                <table class="col-xs-12 table table-condensed table-hover">
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
                        <tr>
                            <td>
                                <strong>Пн</strong>
                            </td>
                            <td>
                                <div id="timeBegin-cont0" class="input-group date">
                                    <?php echo $form->textField($model,'timeBegin0', array(
                                        'id' => 'timeBegin0',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <div id="timeEnd-cont0" class="input-group date">
                                    <?php echo $form->textField($model,'timeEnd0', array(
                                        'id' => 'timeEnd0',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <?php echo $form->dropDownList($model,'cabinet0', $cabinetList, array(
                                    'id' => 'cabinet0',
                                    'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Вт</strong>
                            </td>
                            <td>
                                <div id="timeBegin-cont1" class="input-group date">
                                    <?php echo $form->textField($model,'timeBegin1', array(
                                        'id' => 'timeBegin1',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <div id="timeEnd-cont1" class="input-group date">
                                    <?php echo $form->textField($model,'timeEnd1', array(
                                        'id' => 'timeEnd1',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <?php echo $form->dropDownList($model,'cabinet1', $cabinetList, array(
                                    'id' => 'cabinet1',
                                    'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Ср</strong>
                            </td>
                            <td>
                                <div id="timeBegin-cont2" class="input-group date">
                                    <?php echo $form->textField($model,'timeBegin2', array(
                                        'id' => 'timeBegin2',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <div id="timeEnd-cont2" class="input-group date">
                                    <?php echo $form->textField($model,'timeEnd2', array(
                                        'id' => 'timeEnd2',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <?php echo $form->dropDownList($model,'cabinet2', $cabinetList, array(
                                    'id' => 'cabinet2',
                                    'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Чт</strong>
                            </td>
                            <td>
                                <div id="timeBegin-cont3" class="input-group date">
                                    <?php echo $form->textField($model,'timeBegin3', array(
                                        'id' => 'timeBegin3',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <div id="timeEnd-cont3" class="input-group date">
                                    <?php echo $form->textField($model,'timeEnd3', array(
                                        'id' => 'timeEnd3',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <?php echo $form->dropDownList($model,'cabinet3', $cabinetList, array(
                                    'id' => 'cabinet3',
                                    'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Пт</strong>
                            </td>
                            <td>
                                <div id="timeBegin-cont4" class="input-group date">
                                    <?php echo $form->textField($model,'timeBegin4', array(
                                        'id' => 'timeBegin4',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <div id="timeEnd-cont4" class="input-group date">
                                    <?php echo $form->textField($model,'timeEnd4', array(
                                        'id' => 'timeEnd4',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <?php echo $form->dropDownList($model,'cabinet4', $cabinetList, array(
                                    'id' => 'cabinet4',
                                    'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Сб</strong>
                            </td>
                            <td>
                                <div id="timeBegin-cont5" class="input-group date">
                                    <?php echo $form->textField($model,'timeBegin5', array(
                                        'id' => 'timeBegin5',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <div id="timeEnd-cont5" class="input-group date">
                                    <?php echo $form->textField($model,'timeEnd5', array(
                                        'id' => 'timeEnd5',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <?php echo $form->dropDownList($model,'cabinet5', $cabinetList, array(
                                    'id' => 'cabinet5',
                                    'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Вс</strong>
                            </td>
                            <td>
                                <div id="timeBegin-cont6" class="input-group date">
                                    <?php echo $form->textField($model,'timeBegin6', array(
                                        'id' => 'timeBegin6',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <div id="timeEnd-cont6" class="input-group date">
                                    <?php echo $form->textField($model,'timeEnd6', array(
                                        'id' => 'timeEnd6',
                                        'class' => 'form-control'
                                    )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </td>
                            <td>
                                <?php echo $form->dropDownList($model,'cabinet6', $cabinetList, array(
                                    'id' => 'cabinet6',
                                    'class' => 'form-control'
                                )); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row default-padding x2-default-padding-left">
                <div class="form-group">
                    <?php echo CHtml::ajaxSubmitButton(
                        'Сохранить',
                        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/shedule/addedit'),
                        array(
                            'success' => 'function(data, textStatus, jqXHR) {
                                $("#shedule-by-day-form").trigger("success", [data, textStatus, jqXHR])
                            }'
                        ),
                        array(
                            'class' => 'btn btn-success',
                            'id' => 'doctor-shedule-edit-submit'
                        )
                    ); ?>
                </div>
            </div>
        <?php $this->endWidget(); ?>
    </div>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'shedule-exp-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/shedule/addeditexps'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
    <div class="row col-xs-12">
        <h5><strong>Дни-исключения</strong></h5>
        <p>Если у врача есть дни с особым раписанием, занесите их сюда. Для удаления дня оставьте <strong>все текстовые поля</strong> строки пустыми.</p>
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
                        <td>
                            <div id="day<?php echo $i; ?>-cont" class="input-group date">
                                <?php echo $form->textField($item,"[$i]day", array(
                                    'id' => 'day'.$i,
                                    'class' => 'form-control',
                                    'placeholder' => 'Формат гггг-мм-дд'
                                )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div id="timeBegin-cont<?php echo $i; ?>" class="input-group date">
                                <?php echo $form->textField($item,"[$i]timeBegin", array(
                                    'id' => 'timeBegin'.$i,
                                    'class' => 'form-control'
                                )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div id="timeEnd-cont<?php echo $i; ?>" class="input-group date">
                                <?php echo $form->textField($item,"[$i]timeEnd", array(
                                    'id' => 'timeEnd'.$i,
                                    'class' => 'form-control'
                                )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </td>
                        <td>
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
                    <p>Вы успешно отредактировали расписание.</p>
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