<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/chooser.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/tasuimport.js"></script>
<h4>Импорт приёмов врачей в ТАСУ</h4>
<div class="row">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'tasuimport-filter-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
    <div class="form-group">
        <?php echo $form->label($modelFilter,'doctorId', array(
            'class' => 'col-xs-3 control-label text-left'
        )); ?>
        <div class="col-xs-5">
            <?php echo $form->dropDownList($modelFilter, 'doctorId', array('-1' => 'Нет') + $doctorsList, array(
                'id' => 'filterDoctorId',
                'class' => 'form-control'
            )); ?>
        </div>
    </div>
	<div class="form-group">
		<?php echo $form->labelEx($modelFilter,'greetingDate', array(
			'class' => 'col-xs-3 control-label'
		)); ?>
		<div id="greetingDate2-cont" class="col-xs-5 input-group date">
			<?php echo $form->hiddenField($modelFilter,'greetingDate', array(
				'id' => 'filterGreetingDate',
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
        <input type="button" id="tasuimport-filter-btn" value="Фильтровать" class="btn btn-success">
    </div>
    <?php $this->endWidget(); ?>
</div>
<div class="row importTable">
	<table id="greetings"></table>
	<div id="greetingsPager"></div>
	<div class="btn-group default-margin-top">
		<button type="button" class="btn btn-default" id="addGreeting">Добавить</button>
        <button type="button" class="btn btn-default" id="addAllGreeting">Добавить все приёмы</button>
        <button type="button" class="btn btn-default" id="addFakeGreeting">Добавить приём вручную</button>
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
        <p class="text-success"><strong>Добавлено (врачей): <span class="numDoctorsAdded">0</span></strong></p>
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
<p>(щёлкните два раза на строку, чтобы посмотреть приёмы, прошедшие выгрузку)</p>
<div class="row importHistoryTable">
	<table id="importHistory"></table>
	<div id="importHistoryPager"></div>
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
<div class="modal fade error-popup" id="addFakePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить вручную приём к выгрузке</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'greeting-addfake-form',
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
					<div class="col-xs-5">
						<div class="form-group">
							<?php echo $form->labelEx($modelAddFake,'wardId', array(
								'class' => 'col-xs-3 control-label'
							)); ?>
							<div class="col-xs-9">
								 <?php echo $form->dropDownList($modelAddFake, 'wardId', $wardsList, array(
									'id' => 'wardId',
									'class' => 'form-control',
								)); ?>
							</div>
						</div>
						<div class="form-group">
							<?php echo $form->labelEx($modelAddFake,'doctorId', array(
								'class' => 'col-xs-3 control-label'
							)); ?>
							<div class="col-xs-9">
								 <?php echo $form->dropDownList($modelAddFake, 'doctorId', $doctorsList, array(
									'id' => 'doctorId',
									'class' => 'form-control',
								)); ?>
							</div>
						</div>
						<div class="form-group">
							<?php echo $form->labelEx($modelAddFake,'greetingDate', array(
								'class' => 'col-xs-3 control-label'
							)); ?>
							<div id="greetingDate-cont" class="borderedBox">
								<?php echo $form->hiddenField($modelAddFake,'greetingDate', array(
									'id' => 'greetingDate',
									'class' => 'form-control'
								)); ?>
							</div>
						</div>
						<div class="form-group">
							<?php echo $form->labelEx($modelAddFake,'cardNumber', array(
								'class' => 'col-xs-3 control-label'
							)); ?>
							<div class="col-xs-9">
								<?php echo $form->textField($modelAddFake,'cardNumber', array(
									'id' => 'cardNumber',
									'class' => 'form-control'
								)); ?>
							</div>
						</div>
						<div class="form-group chooser" id="primaryDiagnosisChooser">
							<?php echo $form->labelEx($modelAddFake,'primaryDiagnosis', array(
								'class' => 'col-xs-3 control-label'
							)); ?>
							<div class="col-xs-9">
								<?php echo $form->textField($modelAddFake,'primaryDiagnosis', array(
									'id' => 'primaryDiagnosis',
									'class' => 'form-control'
								)); ?>
								<ul class="variants no-display">
								</ul>
								<div class="choosed">
								</div>
							</div>
						</div>
						<div class="form-group chooser" id="secondaryDiagnosisChooser">
							<?php echo $form->labelEx($modelAddFake,'secondaryDiagnosis', array(
								'class' => 'col-xs-3 control-label'
							)); ?>
							<div class="col-xs-9">
								<?php echo $form->textField($modelAddFake,'secondaryDiagnosis', array(
									'id' => 'secondaryDiagnosis',
									'class' => 'form-control'
								)); ?>
								<ul class="variants no-display">
								</ul>
								<div class="choosed">
								</div>
							</div>
						</div>
						<button type="button" id="greeting-addfake-submit" class="btn btn-success">Сохранить</button>
						<button type="button" id="greeting-addfakeall-submit" class="btn btn-primary" data-dismiss="modal">Закончить ввод приёмов</button>
					</div>
					<div class="col-xs-7">
						<table id="preGreetings"></table>
						<div class="btn-group" id="preGreetings-controls">
							<button type="button" class="btn btn-default" id="deletePreGreeting">Удалить</button>
							<button type="button" class="btn btn-default" id="clearPreGreetings">Очистить</button>
						</div>
					</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="showHistoryGreetingPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Состав выгрузки</h4>
            </div>
            <div class="modal-body">
                <div class="row">
					<table id="historyGreetings"></table>
					<div id="historyGreetingsPager"></div>
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