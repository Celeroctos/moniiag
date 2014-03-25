<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/tasuimport.js"></script>
<h4>Импорт приёмов врачей в ТАСУ</h4>
<div class="row importTable">
	<table id="greetings"></table>
	<div id="greetingsPager"></div>
	<div class="btn-group default-margin-top">
		<button type="button" class="btn btn-default" id="addGreeting">Добавить</button>
        <button type="button" class="btn btn-default" id="addAllGreeting">Добавить все приёмы</button>
		<!--<button type="button" class="btn btn-default" id="editGreeting">Редактировать</button>-->
		<button type="button" class="btn btn-default" id="deleteGreeting">Удалить</button>
		<button type="button" class="btn btn-default" id="importGreetings">Выгрузить</button>
		<button type="button" class="btn btn-default" id="clearGreetings">Очистить</button>
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
        <p class="text-warning">Всего приёмов (строк): <span class="numStringsAll">0</span></p>
        <p class="text-primary">Обработано приёмов (строк): <span class="numStrings">0</span></p>
        <p class="text-success">Добавлено (строк): <span class="numStringsAdded">0</span></p>
        <p class="text-danger">Отклонено (строк): <span class="numStringsDiscarded">0</span></p>
        <p class="text-danger"><strong>Ошибок (строк): <span class="numStringsError">0</span></strong></p>
        <p class="text-success"><strong>Добавлено (медкарт): <span class="numMedcardsAdded">0</span></strong></p>
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
<div class="row importHistoryTable">
	<table id="importHistory"></table>
	<div id="importHistoryPager"></div>
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
<div class="modal fade error-popup" id="confirmPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Очистка задания на выгрузку в ТАСУ</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Вы действительно хотите очистить очередь приёмов для выгрузки?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="submitClearQueue">Да</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Нет</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="addPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить приём к выгрузке</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($modelAdd,'greetingId'),
                'id' => 'greeting-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <?php echo $form->labelEx($modelAdd,'greetingId', array(
                            'class' => 'col-xs-3 control-label'
                        )); ?>
                        <div class="col-xs-9">
                            <?php echo $form->dropDownList($modelAdd,'greetingId', array(), array(
                                'id' => 'greetingId',
                                'class' => 'form-control'
                            )); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/tasu/addgreetingtobuffer'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#greeting-add-form").trigger("success", [data, textStatus, jqXHR])
                            }'
                    ),
                    array(
                        'class' => 'btn btn-primary'
                    )
                ); ?>
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>
