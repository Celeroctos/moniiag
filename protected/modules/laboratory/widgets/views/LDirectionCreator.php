<?php

/**
 * @var LDirectionCreator $this - Self instance
 * @var LDirectionForm $model - Direction's model
 */

$this->beginWidget("CActiveForm", [
    'action' => CHtml::normalizeUrl(""),
    'id' => $this->id,
    'htmlOptions' => [
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    ]
]);

?>

<div>

</div>

<? $this->endWidget(); ?>