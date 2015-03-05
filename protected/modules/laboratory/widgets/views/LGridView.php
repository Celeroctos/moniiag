<?php

/**
 * @var LGridView $this - Self widget instance
 */

$this->widget("zii.widgets.grid.CGridView", [
    "dataProvider" => $this->model->getDataProvider(),
    "filter" => $this->model,
    "id" => $this->id,
    'itemsCssClass' => $this->class,
    "columns" => $this->columns,
    'pager' => [
        'class' => 'CLinkPager',
        'selectedPageCssClass' => 'active',
        'header' => '',
        'htmlOptions' => [
            'class' => 'pagination',
        ]
    ],
    'htmlOptions' => array(
        'class' => 'container',
    ),
    'cssFile' => false
]);