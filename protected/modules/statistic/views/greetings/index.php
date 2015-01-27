<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/chooser.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/statistic/greetings.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/jquery-json.js" ></script>
<script type="text/javascript">
	globalVariables.isMainDoctorCab = true;
</script>
<h4>Статистика по приёмам</h4>
<div class="row">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'patient-search-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/reception/patient/search'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
	<div class="col-xs-6">
        <div class="form-group">
            <?php echo $form->labelEx($modelFilter,'wardId', array(
				'class' => 'col-xs-4 control-label'
			)); ?>
			<div class="col-xs-8">
				 <?php echo $form->dropDownList($modelFilter, 'wardId', $wardsList, array(
					'id' => 'wardId',
					'class' => 'form-control',
					'multiple' => 'multiple',
					'options' => array('-1' => array('selected' => true))
				)); ?>
			</div>
        </div>
		<div class="form-group">
            <?php echo $form->labelEx($modelFilter,'medpersonalId', array(
				'class' => 'col-xs-4 control-label'
			)); ?>
			<div class="col-xs-8">
				 <?php echo $form->dropDownList($modelFilter, 'medpersonalId', $medpersonalList, array(
					'id' => 'medpersonalId',
					'class' => 'form-control',
					'multiple' => 'multiple',
					'options' => array('-1' => array('selected' => true))
				)); ?>
			</div>
        </div>
		<div class="form-group">
			<?php echo $form->labelEx($modelFilter,'doctorId', array(
				'class' => 'col-xs-4 control-label'
			)); ?>
			<div class="col-xs-8">
				 <?php echo $form->dropDownList($modelFilter, 'doctorId', $doctorsList, array(
					'id' => 'doctorId',
					'class' => 'form-control',
					'multiple' => 'multiple',
					'options' => array('-1' => array('selected' => true))
				)); ?>
			</div>
		</div>
		<div class="form-group">
			<?php echo $form->labelEx($modelFilter,'greetingDateFrom', array(
				'class' => 'col-xs-4 control-label'
			)); ?>
			<div id="greetingDate-cont" class="input-group date col-xs-8">
				<?php echo $form->hiddenField($modelFilter,'greetingDateFrom', array(
					'id' => 'filterGreetingDateFrom',
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
			<?php echo $form->labelEx($modelFilter,'greetingDateTo', array(
				'class' => 'col-xs-4 control-label'
			)); ?>
			<div id="greetingDate-cont2" class="input-group date col-xs-8">
				<?php echo $form->hiddenField($modelFilter,'greetingDateTo', array(
					'id' => 'filterGreetingDateTo',
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
            <input type="button" id="greeting-getstat-submit" value="Показать" name="patient-search-submit" class="btn btn-success">
        </div>
	</div>
	<?php $this->endWidget(); ?>
</div>
<div class="row">
	<table id="greetingsStat">
		<!--<tr class="wardHeaderRow">
			<td colspan="6">Наименование отделения</td>
		</tr>
		<tr class="medworkerHeaderRow">
			<td colspan="6">Наименование специальности</td>
		</tr>
		<tr class="dataHeader">
			<td rowspan="2">Врач</td>
			<td rowspan="2">Всего</td>
			<td colspan="2">Первичных</td>
			<td colspan="2">Вторичных</td>
		</tr>
		<tr class="dataHeader2">
			<td>По записи</td>
			<td>Живая очередь</td>
			<td>По записи</td>
			<td>Живая очередь</td>
		</tr>
		<tr class="medworkerFooterRow">
			<td>Итого по специальности</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr class="wardFooterRow">
			<td>Итого по отделению</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr class="allFooterRow">
			<td>Итого</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>-->
	</table>
</div>
<div class="modal fade error-popup" id="errorSearchPopup">
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
<div class="modal fade error-popup" id="notFoundPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Сообщение</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>По введённым поисковым критериям не найдено ни одного приёма. Измените критерии поиска и попробуйте поискать заново.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<table id="greetingsStatHeaders" class="no-display">
	<tr class="wardHeaderRow">
		<td colspan="6">Наименование отделения</td>
	</tr>
	<tr class="medworkerHeaderRow">
		<td colspan="6">Наименование специальности</td>
	</tr>
	<tr class="dataHeader">
		<td rowspan="2">Врач</td>
		<td rowspan="2">Всего</td>
		<td colspan="2">Первичных</td>
		<td colspan="2">Вторичных</td>
	</tr>
	<tr class="dataHeader2">
		<td>По записи</td>
		<td>Живая очередь</td>
		<td>По записи</td>
		<td>Живая очередь</td>
	</tr>
	<tr class="medworkerFooterRow">
		<td>Итого по специальности</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr class="wardFooterRow">
		<td>Итого по отделению</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr class="allFooterRow">
		<td>Итого</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
</table>