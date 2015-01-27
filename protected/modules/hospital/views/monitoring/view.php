<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/hospital/sensors.js" ></script>
<h4>Мониторинг датчиков</h4>
<div class="row">
	<table id="sensorsTable">
		<thead>
			<tr>
				<td>Акт</td>
				<td>Датчик</td>
				<td colspan="3">Пациент</td>
				<td>№ пал</td>
				<td>№ кой</td>
				<td>Температура</td>
				<td>Частота пульса</td>
				<td>Частота дыхания</td>
				<td>Амплитуда ЭКГ</td>
			</tr>
			<tr>
				<td colspan="2">
				<td>Фамилия</td>
				<td>Имя</td>
				<td>Отчество</td>
				<td colspan="6"></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<input type="checkbox" checked="checked" value="" />
				</td>
				<td>1223</td>
				<td>Иванов</td>
				<td>Иван</td>
				<td>Иванович</td>
				<td>5</td>
				<td>7</td>
				<td>35</td>
				<td>56.1</td>
				<td>31.1</td>
				<td>124</td>
			</tr>
			<tr>
				<td>
					<input type="checkbox" checked="checked" value="" />
				</td>
				<td>1223</td>
				<td>Иванов</td>
				<td>Иван</td>
				<td>Иванович</td>
				<td>5</td>
				<td>7</td>
				<td>35</td>
				<td>56.1</td>
				<td>31.1</td>
				<td>124</td>
			</tr>
			<tr>
				<td>
					<input type="checkbox" checked="checked" value="" />
				</td>
				<td>1223</td>
				<td>Иванов</td>
				<td>Иван</td>
				<td>Иванович</td>
				<td>5</td>
				<td>7</td>
				<td>35</td>
				<td class="danger">56.1</td>
				<td>31.1</td>
				<td>124</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="modal fade error-popup" id="sensorEditPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактирование датчика</h4>
            </div>
            <div class="modal-body">
                <div class="row">
					<?php
						$form = $this->beginWidget('CActiveForm', array(
							'id' => 'sensor-edit-form',
							'enableAjaxValidation' => true,
							'enableClientValidation' => true,
							'htmlOptions' => array(
								'class' => 'form-horizontal col-xs-12',
								'role' => 'form'
							)
						));
					?>
					<div class="col-xs-2">
						<div class="sensorIdBlock">5</div>
					</div>
					<div class="col-xs-5">
						<div class="form-group">
							<?php echo $form->label($modelSensorAddEdit,'lastName', array(
								'class' => 'col-xs-3 control-label text-left'
							)); ?>
							<div class="col-xs-9">
								<?php echo $form->textField($modelSensorAddEdit, 'lastName', array(
									'id' => 'lastName',
									'class' => 'form-control'
								)); ?>
							</div>
						</div>
						<div class="form-group">
							<?php echo $form->label($modelSensorAddEdit,'firstName', array(
								'class' => 'col-xs-3 control-label text-left'
							)); ?>
							<div class="col-xs-9">
								<?php echo $form->textField($modelSensorAddEdit, 'firstName', array(
									'id' => 'firstName',
									'class' => 'form-control'
								)); ?>
							</div>
						</div>
						<div class="form-group">
							<?php echo $form->label($modelSensorAddEdit,'middleName', array(
								'class' => 'col-xs-3 control-label text-left'
							)); ?>
							<div class="col-xs-9">
								<?php echo $form->textField($modelSensorAddEdit, 'middleName', array(
									'id' => 'middleName',
									'class' => 'form-control'
								)); ?>
							</div>
						</div>
					</div>
					<div class="col-xs-5">
						<div class="form-group">
							<?php echo $form->label($modelSensorAddEdit,'room', array(
								'class' => 'col-xs-3 control-label text-left'
							)); ?>
							<div class="col-xs-9">
								<?php echo $form->textField($modelSensorAddEdit, 'room', array(
									'id' => 'room',
									'class' => 'form-control'
								)); ?>
							</div>
						</div>
						<div class="form-group">
							<?php echo $form->label($modelSensorAddEdit,'bed', array(
								'class' => 'col-xs-3 control-label text-left'
							)); ?>
							<div class="col-xs-9">
								<?php echo $form->textField($modelSensorAddEdit, 'bed', array(
									'id' => 'bed',
									'class' => 'form-control'
								)); ?>
							</div>
						</div>
						<div class="form-group">
							<?php echo $form->label($modelSensorAddEdit,'middleName', array(
								'class' => 'col-xs-3 control-label text-left'
							)); ?>
							<div class="col-xs-9">
								<?php echo $form->textField($modelSensorAddEdit, 'middleName', array(
									'id' => 'middleName',
									'class' => 'form-control'
								)); ?>
							</div>
						</div>
					</div>
					<?php $this->endWidget(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>