<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/jquery-json.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/ajaxbutton.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/chooser.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/tasumedcardsimport.js"></script>
<?php $this->widget('application.modules.admin.components.widgets.TasuInTabMenu') ?>
<h4>Импорт ЭМК в ТАСУ</h4>
<div class="row importTable">
	<table id="medcards"></table>
	<div id="medcardsPager"></div>
	<div class="btn-group default-margin-top">
		<button type="button" class="btn btn-default" id="addMedcard">Добавить карты вручную</button>
        <!--<button type="button" class="btn btn-default" id="addAllMedcards">Добавить все карты</button>-->
		<button type="button" class="btn btn-default" id="deleteMedcard">Удалить</button>
		<button type="button" class="btn btn-default" id="importMedcards">Выгрузить</button>
		<button type="button" class="btn btn-default" id="clearMedcards">Очистить</button>
        <button type="button" class="btn btn-default" id="printMedcards">Печать списка</button>
	</div>
</div>
<div id="importContainer" class="no-display">
    <div class="row borderedBox default-margin-top progressBox">
        <h5><strong>Прогресс импорта</strong></h5>
        <div class="progress progress-striped active">
            <div class="progress-bar progress-bar-warning" id="importProgressbarP" role="progressbar" aria-valuenow="37" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                <span class="sr-only"></span>
            </div>
        </div>
        <p class="text-warning">Всего медкарт (строк): <span class="numStringsAll">0</span></p>
        <p class="text-primary">Обработано медкарт (строк): <span class="numStrings">0</span></p>
        <p class="text-success">Добавлено (строк): <span class="numStringsAdded">0</span></p>
        <p class="text-danger">Отклонено (строк): <span class="numStringsDiscarded">0</span></p>
        <p class="text-danger"><strong>Ошибок (строк): <span class="numStringsError">0</span></strong></p>
        <p class="text-success"><strong>Добавлено (пациентов): <span class="numPatientsAdded">0</span></strong></p>
        <div class="form-group clear">
            <input type="button" class="btn btn-success successImport no-display" value="Закончить импорт">
            <input type="button" class="btn btn-danger pauseImport" value="Пауза">
            <input type="button" class="btn btn-danger continueImport" value="Продолжить" disabled="disabled">
        </div>
    </div>
    <h4>Лог выгрузки</h4>
    <div class="row logWindow">
        <ul class="list-group">
        </ul>
    </div>
</div>
<h4>История выгрузок</h4>
<p>(щёлкните два раза на строку, чтобы посмотреть медкарты, прошедшие выгрузку)</p>
<div class="row importHistoryTable">
	<table id="importHistory"></table>
	<div id="importHistoryPager"></div>
</div>
<div class="modal fade error-popup" id="addMedcardsPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить карты в выгрузку</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php
                    $form = $this->beginWidget('CActiveForm', array(
                        'id' => 'tasumedcardsimport-filter-form',
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => true,
                        'htmlOptions' => array(
                            'class' => 'form-horizontal col-xs-12',
                            'role' => 'form'
                        )
                    ));
                    ?>
                    <div class="form-group">
                        <?php echo $form->labelEx($modelFilter,'dateFrom', array(
                            'class' => 'col-xs-3 control-label'
                        )); ?>
                        <div id="greetingDate-cont" class="col-xs-5 input-group date">
                            <?php echo $form->hiddenField($modelFilter,'dateFrom', array(
                                'id' => 'dateFrom',
                                'class' => 'form-control'
                            )); ?>
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
                    <div class="form-group">
                        <?php echo $form->labelEx($modelFilter,'dateTo', array(
                            'class' => 'col-xs-3 control-label'
                        )); ?>
                        <div id="greetingDate-cont2" class="col-xs-5 input-group date">
                            <?php echo $form->hiddenField($modelFilter,'dateTo', array(
                                'id' => 'dateTo',
                                'class' => 'form-control'
                            )); ?>
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
                    <div class="form-group">
                        <input type="button" id="tasuimport-filter-btn" value="Найти карты" class="btn btn-success">
                    </div>
                    <?php $this->endWidget(); ?>
                </div>
                <div class="row" id="chooseMedcardsCont">
                    <div class="borderedBox">
                        <table class="table table-condensed table-hover" id="chooseMedcardsTable">
                            <thead>
                                <tr class="header">
                                    <td>
                                        <input type="checkbox" value="-1" title="Отметить всё" id="checkAll" />
                                    </td>
                                    <td>
                                        Номер карты
                                    </td>
                                    <td>
                                        ФИО
                                    </td>
                                    <td>
                                        № док-та
                                    </td>
                                    <td>
                                        Дата рождения
                                    </td>
                                    <td>
                                        Полис
                                    </td>
                                    <td>
                                        Адрес
                                    </td>
                                    <td>
                                        Статус полиса
                                    </td>
                                    <td>
                                        Дата выдачи
                                    </td>
                                    <td>
                                        Страховая компания
                                    </td>
                                    <td>
                                        Регион
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success no-display" id="addChoosedMedcardsBtn">Добавить выбранные карты</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="confirmPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Очистка задания на выгрузку в ТАСУ</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Вы действительно хотите очистить очередь медкарт для выгрузки?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="submitClearQueue">Да</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Нет</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="showHistoryMedcardsPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Состав выгрузки</h4>
            </div>
            <div class="modal-body">
                <div class="row col-xs-12">
                    <table id="historyMedcards"></table>
                    <div id="historyMedcardsPager"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" id="printHistoryMedcards">Печать списка</button>
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