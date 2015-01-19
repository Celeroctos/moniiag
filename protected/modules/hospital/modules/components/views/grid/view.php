<?php
	$this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider' => $dataProvider,
		'enablePagination' => true,
		'enableSorting' => true,
		'summaryCssClass' => 'summaryPanel',
		'id' => $gridId, 
		'ajaxUrl' => array($dataProvider->pagination->route),
		'columns' => array(
			array(
				'name' => 'id',
				'type' => 'raw',
				'value' => '$data->id',
			),
			array(
				'name' => 'fio',
				'type' => 'raw',
				'value' => '$data->last_name." ".$data->first_name." ".($data->middle_name ? $data->middle_name : "")',
			),
			array(
				'name' => 'type_of_writing',
				'type' => 'raw',
				'value' => '',
			),
			array(
				'name' => 'card_number',
				'type' => 'raw',
				'value' => '',
			),
			array(
				'name' => 'age',
				'type' => 'raw',
				'value' => '',
			),
			array(
				'name' => 'number_of_weeks',
				'type' => 'raw',
				'value' => '',
			),
			array(
				'name' => 'hospitalization_date',
				'type' => 'raw',
				'value' => '',
			)
		),
	)); 
?>