<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'patient-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/doctors/shedule/editpatient'),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
));
echo $form->hiddenField($model,'medcardId', array(
    'id' => 'medcardId',
    'class' => 'form-control',
    'value' => $currentPatient
));
?>
<?php function drawCategorie($categorie, $form, $model) { ?>
	        <div id="accordion<?php echo $categorie['id']; ?>" class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a href="#collapse<?php echo $categorie['id']; ?>" data-parent="#accordion<?php echo $categorie['id']; ?>" data-toggle="collapse" class="accordion-toggle"><?php echo $categorie['name']; ?></a>
                </div>
                <div class="accordion-body collapse" id="collapse<?php echo $categorie['id']; ?>">
                    <div class="accordion-inner">
						<?php // Подкатегории 
							if(isset($categorie['children'])) {
								foreach($categorie['children'] as $key => $childCategorie) {
									drawCategorie($childCategorie, $form, $model);
								} 
							}
						?>
                        <?php foreach($categorie['elements'] as $element) { ?>
                            <div class="form-group">
                                <div class="col-xs-3">
                                    <?php echo $form->labelEx($model,'f'.$element['id'], array(
                                        'class' => 'col-xs-12 control-label'
                                    )); ?>
                                </div>
                                <div class="col-xs-9">
                                    <?php
                                    if($element['type'] == 0) {
                                        echo $form->textField($model,'f'.$element['id'], array(
                                            'id' => 'f'.$element['id'],
                                            'class' => 'form-control',
                                            'placeholder' => ''
                                        ));
                                    } elseif($element['type'] == 1) {
                                        echo $form->textArea($model,'f'.$element['id'], array(
                                            'id' => 'f'.$element['id'],
                                            'class' => 'form-control',
                                            'placeholder' => ''
                                        ));
                                    } elseif($element['type'] == 2) {
                                        echo $form->dropDownList($model,'f'.$element['id'], $element['guide'], array(
                                            'id' => 'f'.$element['id'],
                                            'class' => 'form-control',
                                            'placeholder' => '',
                                            'options' => $element['selected']
                                        ));
                                    } elseif($element['type'] == 3) {
                                        echo $form->dropDownList($model,'f'.$element['id'], $element['guide'], array(
                                            'id' => 'f'.$element['id'],
                                            'class' => 'form-control',
                                            'placeholder' => '',
                                            'multiple' => 'multiple',
                                            'options' => $element['selected']
                                        ));
                                    } ?>
                                </div>
                            </div>
                        <?php  } ?>
                    </div>
                </div>
            </div>
        </div>
<?php }?>
<?php foreach($categories  as $index => $template) {
    foreach($template  as $key => $categorie) {
			drawCategorie($categorie, $form, $model);
		} ?>
<?php } ?>
    <div class="form-group submitEditPatient">
        <?php echo CHtml::ajaxSubmitButton(
            'Сохранить',
            CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/doctors/shedule/patientedit'),
            array(
                'success' => 'function(data, textStatus, jqXHR) {
                                $("#patient-edit-form").trigger("success", [data, textStatus, jqXHR])
                            }'
            ),
            array(
                'class' => 'btn btn-primary'
            )
        ); ?>
    </div>
<?php $this->endWidget(); ?>
<div class="modal fade error-popup" id="successEditPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Успешно!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Информация успешно сохранена.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>