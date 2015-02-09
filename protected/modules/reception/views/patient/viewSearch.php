<?php
/**
 * Шаблон для поиска пациентов
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
$this->pageTitle = 'Поиск пациентов';
?>
<h4>Поиск пациентов</h4>
<script type="text/javascript">
//	$(document).ready(function () {
//		$(function () {
//                $('.datetimepicker').datetimepicker({
//					format: 'Y-m-d',
//					timepicker: false,
//					datepicker: true,
//					mask: true,
//					step: 5,
//					lang: 'ru'
//                });
//		});
//	});
</script>
<?php if(Yii::app()->user->checkAccess('searchPatient')): ?>
	<?php
		$form=$this->beginWidget('CActiveForm', [
				'method'=>'get',
	//			'id'=>'patient-search-form',
	//			'enableAjaxValidation'=>true,
	//			'enableClientValidation'=>true,
				'action'=>$this->createUrl('patient/viewsearch'),
				'htmlOptions' => [
					'class' => 'form-horizontal col-xs-12',
					'role' => 'form'
				]
		]);
	?>
	<div class="row">
		<div class="col-xs-6">
			<?= $form->errorSummary($modelOms, '', '', [
				'class'=>'alert alert-warning',
			]); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<div class="form-group">
				<?= $form->Label($modelOms, 'oms_number', ['class'=>'col-xs-4 control-label']); ?>
				<div class="col-xs-6">
					<?= $form->TextField($modelOms, 'oms_number', [
									'class'=>'form-control',
								]); ?>
				</div>
			</div>
			<div class="form-group">
				<?= $form->Label($modelOms, 'card_number', ['class'=>'col-xs-4 control-label']); ?>
				<div class="col-xs-6">
					<?= $form->TextField($modelOms, 'card_number', [
									'class'=>'form-control',
								]); ?>
				</div>
			</div>
			<div class="form-group">
				<?= $form->Label($modelOms, 'last_name', ['class'=>'col-xs-4 control-label']); ?>
				<div class="col-xs-6">
					<?= $form->TextField($modelOms, 'last_name', [
									'class'=>'form-control',
								]); ?>
				</div>
			</div>
			<div class="form-group">
				<?= $form->Label($modelOms, 'first_name', ['class'=>'col-xs-4 control-label']); ?>
				<div class="col-xs-6">
					<?= $form->TextField($modelOms, 'first_name', [
									'class'=>'form-control',
								]); ?>
				</div>
			</div>
			<div class="form-group">
				<?= $form->Label($modelOms, 'middle_name', ['class'=>'col-xs-4 control-label']); ?>
				<div class="col-xs-6">
					<?= $form->TextField($modelOms, 'middle_name', [
									'class'=>'form-control',
								]); ?>
				</div>
			</div>
			<div class="form-group">
				<?= $form->Label($modelOms, 'birthday', ['class'=>'col-xs-4 control-label']); ?>
				<div class="col-xs-6">
					<?= $form->TextField($modelOms, 'birthday', [
									'class'=>'form-control',
								]); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-6 col-xs-offset-4">
					<?= CHtml::submitButton('Найти', [
						'class'=>'btn btn-success'
					]); ?>
				</div>
			</div>
		</div>
		<div class="col-xs-6">
			<div class="form-group">
				<?= $form->Label($modelOms, 'serie', ['class'=>'col-xs-4 control-label']); ?>
				<div class="col-xs-6">
					<?= $form->TextField($modelOms, 'serie', [
									'class'=>'form-control',
								]); ?>
				</div>
			</div>
			<div class="form-group">
				<?= $form->Label($modelOms, 'docnumber', ['class'=>'col-xs-4 control-label']); ?>
				<div class="col-xs-6">
					<?= $form->TextField($modelOms, 'docnumber', [
									'class'=>'form-control',
								]); ?>
				</div>
			</div>
			<div class="form-group">
				<?= $form->Label($modelOms, 'address_reg', ['class'=>'col-xs-4 control-label']); ?>
				<div class="col-xs-6">
					<?= $form->TextField($modelOms, 'address_reg', [
									'class'=>'form-control',
								]); ?>
				</div>
			</div>
			<div class="form-group">
				<?= $form->Label($modelOms, 'address', ['class'=>'col-xs-4 control-label']); ?>
				<div class="col-xs-6">
					<?= $form->TextField($modelOms, 'address', [
									'class'=>'form-control',
								]); ?>
				</div>
			</div>
			<div class="form-group">
				<?= $form->Label($modelOms, 'snils', ['class'=>'col-xs-4 control-label']); ?>
				<div class="col-xs-6">
					<?= $form->TextField($modelOms, 'snils', [
									'class'=>'form-control',
								]); ?>
				</div>
			</div>
		</div>
	</div>
	<?php $this->endWidget(); ?>

	<?php
		$this->widget('zii.widgets.grid.CGridView', [
			'dataProvider'=>$modelOms->search(),
//			'filter'=>$modelOms,
			'ajaxUpdate' =>true,
			'enableSorting'=>false,
			'itemsCssClass'=>'table table-bordered',
			'pager'=>[
				'class'=>'CLinkPager',
				'selectedPageCssClass'=>'active',
				'header'=>'',
				'htmlOptions'=>[
					'class'=>'pagination',
				]
			],
			'columns'=>[
				[
					'name'=>'oms_number',
					'filter'=>false,
					'header'=>'№ полиса',
						'htmlOptions'=>[
						'class'=>'col-xs-1',
					],
				],
				[
					'name'=>'last_name',
					'filter'=>false,
					'header'=>'Фамилия',
						'htmlOptions'=>[
						'class'=>'col-xs-1',
					],
				],
				[
					'name'=>'first_name',
					'filter'=>false,
					'header'=>'Имя',
						'htmlOptions'=>[
						'class'=>'col-xs-1',
					],
				],
				[
					'name'=>'middle_name',
					'filter'=>false,
					'header'=>'Отчество',
						'htmlOptions'=>[
						'class'=>'col-xs-1',
					],
				],
				[
					'name'=>'birthday',
					'filter'=>false,
					'header'=>'День рождения',
						'htmlOptions'=>[
						'class'=>'col-xs-1',
					],
				],
				[
					'name'=>'lastMedcard',
					'filter'=>false,
					'type'=>'raw',
					'value'=>'$data->getLastMedcard($data->id)',
					'header'=>'№ Медкарты',
						'htmlOptions'=>[
						'class'=>'col-xs-1',
					],
				],
			],
			'itemsCssClass'=>'table table-bordered',
			'pagerCssClass'=>'',
		]);
	?>
<?php endif;