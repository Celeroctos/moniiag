<!--<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/analysistypes.js"></script>
-->
<!-- <table id="analysistypes"></table>
<div id="analysistypesPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addAnalysisType">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editAnalysisType">Редактировать запись</button>
    <button type="button" class="btn btn-default" id="deleteAnalysisType">Удалить запись</button>
</div>
-->
<!--
<div class="modal fade" id="addAnalysisTypePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить тип анализа</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'analysistype-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/analysistypes/add'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
            <div class="modal-body">
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
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/analysistypes/add'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                        $("#analysistype-add-form").trigger("success", [data, textStatus, jqXHR])
                        }'
                    ),
                    array(
                        'class' => 'btn btn-primary'
                    )
                ); ?>
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>


<div class="modal fade" id="editAnalysisTypePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать тип анализа</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'analysistype-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/analysistypes/update'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <?php echo $form->hiddenField($model,'id', array(
                                'id' => 'id',
                                'class' => 'form-control'
                            )); ?>
                        </div>
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
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/analysistypes/update'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                        $("#analysistype-edit-form").trigger("success", [data, textStatus, jqXHR])
                        }'
                    ),
                    array(
                        'class' => 'btn btn-primary'
                    )
                ); ?>
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="errorAddAnalysisTypePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Ошибка!</h4>
            </div>
            <div class="modal-body">
                <h4>При заполнении формы возникли следующие ошибки:</h4>
                <div class="row">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
-->

<?php
/**
* Шаблон вывода отделений
* @author Dzhamal Tayibov <prohps@yandex.ru>
*/
$this->pageTitle = 'Типы анализов';
?>
<?php if(Yii::app()->user->hasFlash('error') || Yii::app()->user->hasFlash('success')): ?>
    <div class="alert alert-danger">
        <?= Yii::app()->user->getFlash('error'); ?>
        <?= Yii::app()->user->getFlash('success'); ?>
    </div>
    <?php endif; ?>


    <?php if(Yii::app()->user->checkAccess('guideAddAnalysisType')) { ?>
<?= CHtml::link('Добавить', $this->createUrl('/guides/analysistypes/create'), [
    'class'=>'btn btn-primary'
]);  
?>
        <?php } ?>

<?php

$template = '{view} ';
$button = ['view' => ['label'=>'Просмотреть',
                    'imageUrl'=>false,
                    'options'=>[
                        'class'=>'btn btn-primary btn-block btn-xs'
                    ]
        ]
    ];
if(Yii::app()->user->checkAccess('guideEditAnalysisType')) {
    $template .= '{update} ';
    $button['update'] = ['label'=>'Редактировать',
                    'imageUrl'=>false,
                    'options'=>[
                        'class'=>'btn btn-primary btn-block btn-xs'
                    ],
                    ];
}
if(Yii::app()->user->checkAccess('guideDeleteAnalysisType')) {
    $template .= '{delete}';
    $button['delete'] = ['label'=>'Удалить',
                    'imageUrl'=>false,
                    'options'=>[
                        'class'=>'btn btn-default btn-block btn-xs'
                    ],
                    ];
}
                $button['headerHtmlOptions']=[
                    'class'=>'col-md-1',
                ];

$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'ajaxUpdate'=>false,
    'itemsCssClass'=>'table table-bordered',
    'pager'=>[
        'class'=>'CLinkPager',
        'selectedPageCssClass'=>'active',
        'header'=>'',
        'htmlOptions'=>[
            'class'=>'pagination',
        ]
    ],
    'columns'=>[
        [
            'name'=>'id',
            'headerHtmlOptions'=>[
                'class'=>'col-md-1',
            ],
        ],
        [
            'name'=>'name',
            'headerHtmlOptions'=>[
                'class'=>'col-md-4',
            ],
        ],
        [
            'name'=>'short_name',
            'headerHtmlOptions'=>[
                'class'=>'col-md-2',
            ],
        ],
        [
            'name'=>'automatic',
            'value'=>'$data->getBool($data->automatic)',
            'headerHtmlOptions'=>[
                'class'=>'col-md-1',
            ],
        ],
        [
            'name'=>'manual',
            'value'=>'$data->getBool($data->manual)',
            'headerHtmlOptions'=>[
                'class'=>'col-md-1',
            ],
        ],
        [
            'class'=>'CButtonColumn',
            'deleteConfirmation' => "js:'Вы уверены, что хотите удалить тип анализа с #ID '+$(this).parent().parent().children(':first-child').text() + '?'",
            'template' => $template /*'{update} {delete}'*/,
//            'buttons'=>$button
/*        [
                'update'=>[
                    'label'=>'Редактировать',
                    'imageUrl'=>false,
                    'options'=>[
                        'class'=>'btn btn-primary btn-block btn-xs'
                    ],

                ],
                'delete'=>[
                    'label'=>'Удалить',
                    'imageUrl'=>false,
                    'options'=>[
                        'class'=>'btn btn-default btn-block btn-xs'
                    ],
                ],
                'headerHtmlOptions'=>[
                    'class'=>'col-md-1',
                ],
            ]
 * ,
 */
        ],
    ],
]
        );
?>
