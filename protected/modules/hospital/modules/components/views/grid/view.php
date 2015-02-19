<?php
    $cs = Yii::app()->clientScript;
    $cs->scriptMap=array(
        'jquery.js' => false
    );

    // Datepickers
    foreach($columns as &$column) {
        if(isset($column['filter']) && $column['filter'] == 'date') {
            $column['filter'] = $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model' => $model,
                'attribute' => $column['name'],
                'language' => 'ru',
                'htmlOptions' => array(
                    'id' => $column['name'].'_datepicker'
                ),
                'options' => array(
                    'showOn' => 'focus',
                    'dateFormat' => 'dd.mm.yy',
                    'showOtherMonths' => true,
                    'selectOtherMonths' => true,
                    'changeMonth' => true,
                    'changeYear' => true,
                    'showButtonPanel' => true,
                ),
            ), true);
        }
    }

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