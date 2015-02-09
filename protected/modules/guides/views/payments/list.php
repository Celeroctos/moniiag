<?php
/**
 * Шаблон вывода типов оплат
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
$this->pageTitle = 'Типы оплат';
?>
<?php if(Yii::app()->user->hasFlash('success')): ?>
	<div class="alert alert-success">
		<?= Yii::app()->user->getFlash('success'); ?>
	</div>
<?php endif; ?>
<?= CHtml::errorSummary($model, '', '', [
				'class'=>'alert alert-warning',
			]); ?>
<?php
$this->widget('zii.widgets.grid.CGridView', [
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'ajaxUpdate'=>false,
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
			'name'=>'id',
			'headerHtmlOptions'=>[
				'class'=>'col-md-7',
			],
		],
		[
			'name'=>'name',
			'headerHtmlOptions'=>[
						'class'=>'col-md-2',
				],
		],
		[
			'name'=>'tasu_string',
			'headerHtmlOptions'=>[
						'class'=>'col-md-2',
				],
		],
		[
			'class'=>'CButtonColumn',
			'buttons'=>[
				'view'=>[
					'label'=>'Просмотр',
					'visible'=>'',
					'imageUrl'=>false,
					'options'=>[
						'class'=>'btn btn-primary btn-block btn-xs'
					],
				],
				'update'=>[
					'label'=>'Редактировать',
					'imageUrl'=>false,
					'options'=>[
						'class'=>'btn btn-primary btn-block btn-xs'
					],
					
				],
				'delete'=>[
					'label'=>'Удалить',
					'imageUrl'=>false,
					'options'=>[
						'class'=>'btn btn-default btn-block btn-xs'
					],
				],
				'headerHtmlOptions'=>[
					'class'=>'col-md-1',
				],
			],
		],
	],
]);
?>
<?= CHtml::link('Добавить', $this->createUrl('payments/create'), [
					'class'=>'btn btn-primary'
				]); ?>