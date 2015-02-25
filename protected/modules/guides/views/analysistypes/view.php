<?php
/**
* Шаблон обновления отделения
* @author Dzhamal Tayibov <prohps@yandex.ru>
*/
$this->pageTitle='Просмотр типа анализа';
?>
    <h3>
    <?php
    echo 'Просмотр типа анализа #ID ' . $model->id;
    ?>
    </h3>
<?php if(Yii::app()->user->hasFlash('success')): ?>
    <div class="alert alert-success">
        <?= Yii::app()->user->getFlash('success'); ?>
    </div>
    <?php endif; ?>
<?php
$form=$this->beginWidget('CActiveForm', [
    'action'=>$this->createUrl('analysistypes/view', ['id'=>$model->id]),
    'htmlOptions' => [
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    ]
]);
?>
<div class="row">
    <div class="col-xs-12">
        <?= $form->errorSummary($model, '', '', [
            'class'=>'alert alert-warning',
        ]); ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="form-group">
            <?php echo $form->labelEx($model,'name', array(
                'class' => 'col-xs-3 control-label'
            )); ?>
            <div class="col-xs-9">
                <?php echo $form->textField($model,'name', array(
                    'id' => 'name',
                    'class' => 'form-control',
                    'placeholder' => 'Наименование анализа'
                )); ?>
                <?php echo $form->error($model,'value'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'short_name', array(
                'class' => 'col-xs-3 control-label'
            )); ?>
            <div class="col-xs-9">
                <?php echo $form->textField($model,'short_name', array(
                    'id' => 'short_name',
                    'class' => 'form-control',
                    'placeholder' => 'Краткое наименование анализа'
                )); ?>
                <?php echo $form->error($model,'short_name'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'automatic', array(
                'class' => 'col-xs-3 control-label'
            )); ?>
            <div class="col-xs-9">
                <?php echo $form->checkBox($model,'automatic', array(
                    /*'id' => 'automatic',*/
                    'class' => 'form-control'/*,
                    'placeholder' => 'Полное название'*/
                )); ?>
                <?php echo $form->error($model,'automatic'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'manual', array(
                'class' => 'col-xs-3 control-label'
            )); ?>
            <div class="col-xs-9">
                <?php echo $form->checkBox($model,'manual', array(
                    /*'id' => 'manual',*/
                    'class' => 'form-control'/*,
                    'placeholder' => 'Полное название'*/
                )); ?>
                <?php echo $form->error($model,'manual'); ?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-7 col-xs-offset-2">
                <?= CHtml::link('Вернуться назад', $this->createUrl('analysistypes/view'), [
                    'class'=>'btn btn-default'
                ]); ?>
            </div>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>