<!--<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/analysisparams.js"></script>
-->
<!-- <table id="analysisparams"></table>
<div id="analysisparamsPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addAnalysisParam">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editAnalysisParam">Редактировать запись</button>
    <button type="button" class="btn btn-default" id="deleteAnalysisParam">Удалить запись</button>
</div>
-->
<!--<div class="modal fade" id="addAnalysisParamPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить тип анализа</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'analysisparam-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/analysisparams/add'),
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
                            <?php echo $form->labelEx($model,'long_name', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'long_name', array(
                                    'id' => 'long_name',
                                    'class' => 'form-control',
                                    'placeholder' => 'Полное наименование параметра анализа'
                                )); ?>
                                <?php echo $form->error($model,'long_name'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/analysisparams/add'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                        $("#analysisparam-add-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade" id="editAnalysisParamPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать тип анализа</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'analysisparam-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/analysisparams/update'),
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
                            <?php echo $form->labelEx($model,'long_name', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'long_name', array(
                                    'id' => 'long_name',
                                    'class' => 'form-control',
                                    'placeholder' => 'Полное наименование параметра анализа'
                                )); ?>
                                <?php echo $form->error($model,'long_name'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/analysisparams/update'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                        $("#analysisparam-edit-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade error-popup" id="errorAddAnalysisParamPopup">
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


    <?php if(Yii::app()->user->checkAccess('guideAddAnalysisParam')) { ?>
<?= CHtml::link('Добавить', $this->createUrl('/guides/analysisparams/create'), [
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
if(Yii::app()->user->checkAccess('guideEditAnalysisParam')) {
    $template .= '{update} ';
    $button['update'] = ['label'=>'Редактировать',
                    'imageUrl'=>false,
                    'options'=>[
                        'class'=>'btn btn-primary btn-block btn-xs'
                    ],
                    ];
}
if(Yii::app()->user->checkAccess('guideDeleteAnalysisParam')) {
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
            'name'=>'long_name',
            'headerHtmlOptions'=>[
                'class'=>'col-md-4',
            ],
        ],
        [
            'class'=>'CButtonColumn',
            'deleteConfirmation' => "js:'Вы уверены, что хотите удалить параметр анализа с #ID '+$(this).parent().parent().children(':first-child').text() + '?'",
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
]);
