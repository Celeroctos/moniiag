<script type="text/javascript">
    globalVariables.guideEdit = '<?php echo Yii::app()->user->checkAccess('guideEditAnalysisTypeTemplate'); ?>';</script>

<h4>Справочники лаборатории</h4>
<?php
$this->widget('application.modules.guides.components.widgets.LaboratoryTabMenu', array());
?>
<?php
$this->pageTitle = 'Шаблоны типов анализов';
?>
<h4>Шаблоны типов анализов</h4>

<?php if (Yii::app()->user->hasFlash('error') || Yii::app()->user->hasFlash('success')): ?>
    <div class="alert alert-danger">
        <?= Yii::app()->user->getFlash('error'); ?>
        <?= Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>

<?php
$updateDialog = <<<'EOT'
function() {
	var url = $(this).attr('href');
    $.get(url, function(r){
        $("#update").html(r).dialog("open");
		$("#DialogCRUDForm").html(r).dialog("option", "title", "Редактирование шаблона типа анализа").dialog("open");
    });
    return false;
}
EOT;
?>

<?php 
$template = '';
if (Yii::app()->user->checkAccess('guideEditAnalysisTypeTemplate')) { 
    $template = '{update}';
    $buttons = array(
                'headerHtmlOptions' => array(
                    'class' => 'col-md-1',
                ),
                'update' => array(
                    'click' => $updateDialog,
                    'imageUrl' => false,
                    'options' => [
                        'class' => 'btn btn-primary btn-block btn-xs',
                    ],
                ),
/*                'delete' => array(
                    'imageUrl' => false,
                    'options' => [
                        'class' => 'btn btn-default btn-block btn-xs',
                    ],
                )*/
        );

    ?>
<?php } ?>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'DialogCRUDForm', 'options' => array(
        'autoOpen' => false,
        'modal' => true,
        'width' => 'auto',
        'height' => 'auto',
        'resizable' => 'false',
    ),
        )
);
        $this->endWidget();
?>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'analysis-param-grid',
    'ajaxUpdate' => false,
//    'dataProvider'=>$model->search(),
    'dataProvider' => $model->templates(),
//	'filter'=>$model,
    'itemsCssClass' => 'table table-bordered',
    'pager' => [
        'class' => 'CLinkPager',
        'selectedPageCssClass' => 'active',
        'header' => '',
        'htmlOptions' => [
            'class' => 'pagination',
        ]
    ],
    'columns' => array(
#		'id',
        [
            'name' => 'name',
            'headerHtmlOptions' => [
                'class' => 'col-md-4',
            ],
        ],
        [
            'name' => 'param_count',
            'headerHtmlOptions' => [
                'class' => 'col-md-4',
            ],
        ],
#        'analysis_type_id',
#        'analysis_param_id',
#	'is_default',
        array(
            'class' => 'CButtonColumn',
            'template' => $template,
            'buttons' => $buttons,
        ),
    ),
));
?>
      