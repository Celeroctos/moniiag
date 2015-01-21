<?
/**
 * @var LForm $this - Form widget instance
 * @var CActiveForm $form - Form widget
 * @var LFormModel $model - Form model
 */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4><? if ($this->title != null) { print $this->title; } ?></h4>
    </div>
    <div class="panel-body">
        <? $form = $this->beginWidget('CActiveForm', array(
            'focus' => array($model,'name'),
            'id' => $this->id,
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'action' => CHtml::normalizeUrl(Yii::app()->getBaseUrl().$this->url),
            'htmlOptions' => array(
                'class' => 'form-horizontal col-xs-12',
                'role' => 'form',
                'data-form' => get_class($this)
            )
        )); ?>
        <? foreach ($model->getContainer() as $key => $value): ?>
            <div class="form-group">
                <?php if (true) {
                    echo $form->labelEx($model, $key, array(
                        'class' => 'col-xs-3 control-label'
                    ));
                } ?>
                <div class="col-xs-9">
                    <?= $this->renderField($form, $model, $key); ?>
                </div>
            </div>
        <? endforeach; ?>
        <? $this->endWidget(); ?>
    </div>
</div>
<script>
    $(document).ready(function() {
        console.log($("#<?= $this->id ?>"));
    });
</script>