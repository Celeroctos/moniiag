<?
/**
 * @var LForm $this - Form widget instance
 * @var CActiveForm $form - Form widget
 * @var LFormModel $model - Form model
 */
?>

<? $form = $this->beginWidget('CActiveForm', [
    'focus' => [
        $this->model, 'name'
    ],
    'id' => $this->id,
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'action' => CHtml::normalizeUrl($this->url),
    'htmlOptions' => [
        'class' => 'form-horizontal col-xs-12 col-xs-offset-1',
        'role' => 'form',
        'data-form' => get_class($this->model),
        'data-widget' => get_class($this)
    ]
]); ?>
<? foreach ($model->getContainer() as $key => $value): ?>
    <div class="form-group">
        <?php if (!$this->checkType($key, "Hidden")) {
            echo $form->labelEx($model, $key, array(
                'class' => 'col-xs-4 control-label'
            ));
        } ?>
        <div class="col-xs-7">
            <?= $this->renderField($form, $key); ?>
        </div>
        <? if ($this->checkType($key, "DropDown")): ?>
            <a href="javascript:void(0)">
                <span style="font-size: 15px; margin-left: -15px; margin-top: 5px" class="col-xs-1 glyphicon glyphicon-search"></span>
            </a>
        <? endif; ?>
    </div>
<? endforeach; ?>
<? $this->endWidget(); ?>