<?php

    /* $columns[] = array(
        'class' => 'CButtonColumn',
        'template' => '{update}&nbsp;{delete}',
        'buttons' => array(
            'update' => array(
                //url до картинки
                'imageUrl'=>'/images/icons/edit.png',
                //здесь должен быть url для редактирования записи
                'url' => 'Yii::app()->createUrl("/edit/$data->id")',
            ),
            'delete' => array(
                //url до картинки
                'imageUrl'=>'/images/icons/delete.png',
                //здесь должен быть url для удаления записи
                'url' => 'Yii::app()->createUrl("/delete/$data->id")',
            ),
        ),
    ); */

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