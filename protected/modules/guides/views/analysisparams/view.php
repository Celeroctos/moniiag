<?php
/**
 * Шаблон обновления отделения
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
$this->pageTitle = 'Просмотр тиапа анализа';
?>
    <h3>
    <?php
    echo 'Просмотр параметра анализа #ID ' . $model->id;
    ?>
    </h3>
<?php
$form = $this->beginWidget('CActiveForm', [
    'action' => $this->createUrl('analysisparams/view', ['id' => $model->id]),
    'htmlOptions' => [
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    ]
        ]);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="form-group">
            <?php
            echo $form->labelEx($model, 'name', array(
                'class' => 'col-xs-3 control-label'
            ));
            ?>
            <div class="col-xs-9">
                <?php
                echo $form->textField($model, 'name', array(
                    'id' => 'name',
                    'class' => 'form-control',
                    'placeholder' => 'Краткое наименование параметра анализа'
                ));
                ?>
<?php echo $form->error($model, 'value'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php
            echo $form->labelEx($model, 'long_name', array(
                'class' => 'col-xs-3 control-label'
            ));
            ?>
            <div class="col-xs-9">
                <?php
                echo $form->textField($model, 'long_name', array(
                    'id' => 'long_name',
                    'class' => 'form-control',
                    'placeholder' => 'Полное наименование параметра анализа'
                ));
                ?>
<?php echo $form->error($model, 'long_name'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php
            echo $form->labelEx($model, 'comment', array(
                'class' => 'col-xs-3 control-label'
            ));
            ?>
            <div class="col-xs-9">
                <?php
                echo $form->textArea($model, 'comment', array(
                    'id' => 'comment',
                    'class' => 'form-control',
                    'placeholder' => 'Примечания'
                ));
                ?>
<?php echo $form->error($model, 'comment'); ?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-7 col-xs-offset-2">
<?=
CHtml::link('Вернуться назад', $this->createUrl('analysisparams/list'), [
    'class' => 'btn btn-default'
]);
?>
            </div>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>