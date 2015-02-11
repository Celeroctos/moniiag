<?php
/**
 * Шаблон вывода медперсонала
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
$this->pageTitle = 'Отделения';
?>
<?= CHtml::link('Добавить', $this->createUrl('medworkers/create'), [
					'class'=>'btn btn-primary'
				]); ?>
<?php
$this->widget('zii.widgets.grid.CGridView', [
	'dataProvider'=>$model->search(),
//	'filter'=>$model,
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
				'class'=>'col-md-1',
			],
		],
		[
			'name'=>'name',
			'headerHtmlOptions'=>[
						'class'=>'col-md-4',
				],
		],
		[
			'name'=>'medpersonal_type.name',
			'headerHtmlOptions'=>[
						'class'=>'col-md-2',
				],
		],
		[
			'name'=>'payment_type',
			'value'=>'$data->getPayment_type($data->payment_type)',
			'headerHtmlOptions'=>[
						'class'=>'col-md-4',
				],
		],
		[
			'name'=>'is_medworker',
			'value'=>'$data->getIs_medworker($data->is_medworker)',
			'headerHtmlOptions'=>[
						'class'=>'col-md-4',
				],
		],
		[
			'name'=>'is_for_pregnants',
			'value'=>'$data->getIs_for_pregnants($data->is_for_pregnants)',
			'headerHtmlOptions'=>[
						'class'=>'col-md-4',
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
	'pagerCssClass'=>'',
	'itemsCssClass'=>'table table-bordered',
]);