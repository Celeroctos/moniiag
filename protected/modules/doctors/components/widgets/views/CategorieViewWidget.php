<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/doctors/patient.js"></script>
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
    <div id="accordionX" class="accordion">
        <div class="accordion-group">
            <div class="accordion-heading">
                <a href="#collapseX" data-parent="#accordionX" data-toggle="collapse" class="accordion-toggle">Реквизитная информация</a>
            </div>
            <div class="accordion-body collapse" id="collapseX">
                <div class="accordion-inner">
                    <p>HTML stands for HyperText Markup Language. HTML is the main markup language for describing the structure of Web pages. <a href="http://www.tutorialrepublic.com/html-tutorial/" target="_blank">Learn more.</a></p>
                </div>
            </div>
        </div>
    </div>
<?php foreach($categories  as $index => $template) {
    foreach($template  as $key => $categorie) {
        ?>
        <div id="accordion<?php echo $index; ?>" class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a href="#collapse<?php echo $categorie['id']; ?>" data-parent="#accordion<?php echo $index; ?>" data-toggle="collapse" class="accordion-toggle"><?php echo $categorie['name']; ?></a>
                </div>
                <div class="accordion-body collapse in" id="collapse<?php echo $categorie['id']; ?>">
                    <div class="accordion-inner">
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
    <?php } ?>
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