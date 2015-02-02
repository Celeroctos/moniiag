<?php
/**
 * Шаблон для поиска пациентов
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
$this->pageTitle = 'Поиск пациентов';
?>
<h3>Поиск пациентов</h3>
<!--<script type="text/javascript">
	$(document).ready(function () {
		$('.inputRedact').on('change', function () {
			$(this).parent().submit();
		});
	});
</script>-->

<?php
$this->widget('zii.widgets.grid.CGridView', [
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'ajaxUpdate' => false,
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
			'header'=>'№',
		    	'htmlOptions'=>[
				'class'=>'col-md-1',
			],
		],
	],
	'itemsCssClass'=>'table table-bordered',
	'pagerCssClass'=>'',
]);