<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/ajaxbutton.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/chooser.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/logs.js" ></script>
<h4>Лог работы системы</h4>
<div class="row">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'log-search-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/logs/search'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
    <div class="col-xs-6">
        <div class="form-group">
            <label for="date" class="col-xs-4 control-label required">Дата</label>
            <div id="date-cont" class="col-xs-3 input-group date">
                <input type="hidden" name="birthday2" placeholder="Формат гггг-мм-дд" class="form-control col-xs-4" id="date">
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
        <div class="form-group chooser" id="userChooser">
            <label for="categorie" class="col-xs-4 control-label">Пользователь (Enter - добавить)</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" autofocus id="patient" placeholder="ФИО пациента">
                <ul class="variants no-display">
                </ul>
                <div class="choosed">
                </div>
            </div>
        </div>
        <div class="form-group">
            <input type="button" id="logs-search-submit" value="Найти" name="logs-search-submit" class="btn btn-success">
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>
<div class="row no-display" id="logsSearchCont">
    <div class="col-xs-12 borderedBox">
        <table class="table table-condensed table-hover" id="logsSearchResult">
            <thead>
            <tr class="header">
                <td>
                    ID
                </td>
                <td>
                    Логин
                </td>
                <td>
                    Запрос
                </td>
                <td>
                    Дата изменения
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
</div>