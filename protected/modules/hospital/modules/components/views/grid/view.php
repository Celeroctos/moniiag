<?php
    $cs = Yii::app()->clientScript;
    $cs->scriptMap=array(
        'jquery.js' => false
    );

    $this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider' => $dataProvider,
		'enablePagination' => true,
		'enableSorting' => true,
		'summaryCssClass' => 'summaryPanel',
		'id' => $gridId, 
		'ajaxUrl' => array($dataProvider->pagination->route),
        'ajaxUpdate' => true,
		'columns' => $columns,
        'filter' => $model,
        'beforeAjaxUpdate' => 'function(id, xhr) {
            xhr.url += "returnAsJson=1&serverModel='.$serverModel.'";
        }',
        'afterAjaxUpdate' => 'function(id, htmlData) {
            $("'.$container.'").css({
                "textAlign" : "left"
            }).html(htmlData);
        }'
	)); 
?>