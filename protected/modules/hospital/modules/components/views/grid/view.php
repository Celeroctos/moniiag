<?php

	$this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider' => $dataProvider,
		'enablePagination' => true,
		'enableSorting' => true,
		'summaryCssClass' => 'summaryPanel',
		'id' => $gridId, 
		'ajaxUrl' => array($dataProvider->pagination->route),
		'columns' => $columns
	)); 
?>