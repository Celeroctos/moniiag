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
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form',
        'data-form' => get_class($this->model),
        'data-widget' => get_class($this)
    ]
]); ?>
<? foreach ($model->getContainer() as $key => $value): ?>
    <div class="form-group <?= $this->isHidden($key) ? "hidden" : "" ?>">
        <?php if (!$this->checkType($key, "Hidden")) {
            echo $form->labelEx($model, $key, array(
                'class' => 'col-xs-5 control-label'
            ));
        } ?>
        <div class="col-xs-6">
            <?= $this->renderField($form, $key); ?>
        </div>
        <? if ($this->checkType($key, "DropDown")): ?>
            <a href="javascript:void(0)"><span style="font-size: 15px; margin-left: -15px; margin-top: 5px" class="col-xs-1 glyphicon glyphicon-search form-search-button"></span></a>
        <? elseif ($this->checkType($key, "Multiple")): ?>
            <a href="javascript:void(0)"><span style="font-size: 15px; margin-left: -15px; margin-top: 5px" class="col-xs-1 glyphicon glyphicon-arrow-up form-up-button"></span></a>
            <a href="javascript:void(0)"><span style="font-size: 15px; margin-left: -15px; margin-top: 5px" class="col-xs-1 glyphicon glyphicon-arrow-down form-down-button"></span></a>
        <? endif; ?>
    </div>
<? endforeach; ?>
<? $this->endWidget(); ?>