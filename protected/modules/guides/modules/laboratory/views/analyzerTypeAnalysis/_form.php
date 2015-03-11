<div class="modal-dialog">
    <!--<div class="form">-->

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'analyzer-type-analysis-form',
        'enableAjaxValidation' => false,
    ));
    ?>

    <div class="modal-body">
        <div class="col-xs-12">
            <div class="row"> 
                <div class="form-group">
                    <?php
                    echo $form->labelEx($model, 'analyser_type_id', array(
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
<?php echo $form->error($model, 'analyser_type_id'); ?>
                    </div>
                </div>
            </div>
            <div class="row"> 
                <div class="form-group">
                        <?php
                        echo $form->labelEx($model, 'analysis_type_id', array(
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
<?php echo $form->error($model, 'analysis_type_id'); ?>
                    </div>
                </div> 
            </div> 
        </div> 
    </div> 

    <div class="modal-footer">
        <?php
        $this->widget('zii.widgets.jui.CJuiButton', array(
            'name' => 'submit_' . rand(),
            'caption' => $model->isNewRecord ? 'Создать' : 'Сохранить',
            'htmlOptions' => array(
                'class' => 'btn btn-primary',
                'ajax' => array(
                    'url' => $model->isNewRecord ? $this->createUrl('create') : $this->createUrl('update', array('id' => $model->id)),
                    'type' => 'post',
                    'data' => 'js:jQuery(this).parents("form").serialize()',
                    'success' => 'function(r){
                                    if(r=="success"){
					window.location.reload();
                                    }
                                    else{
					$("#DialogCRUDForm").html(r).dialog("option", "title", "' . ($model->isNewRecord ? 'Create' : 'Update') . ' AnalyzerTypeAnalysis").dialog("open"); return false;
                                    }
				}',
                ),
            ),
        ));

        $this->widget('zii.widgets.jui.CJuiButton', array(
            'buttonType' => 'button',
            'name' => 'close_' . rand(),
            'caption' => 'Закрыть',
            'htmlOptions' => array('class' => 'btn btn-default',
                'ajax' => array(
                    'url' => '#',
                    'type' => 'post',
                    'success' => 'function(r){
                                    window.location.reload();
				}',
                ),
            ),
        ));
        ?>

<?php $this->endWidget(); ?>
    </div>

</div><!-- form -->