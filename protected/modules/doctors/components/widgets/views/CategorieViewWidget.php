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
echo $form->hiddenField($model,'greetingId', array(
    'id' => 'greetingId',
    'class' => 'form-control',
    'value' => $greetingId
));
?>
<?php foreach($categories  as $index => $template) {
    foreach($template  as $key => $categorie) {
			$this->drawCategorie($categorie, $form, $model);
		} ?>
<?php } ?>
    <?php if(!$withoutSave && Yii::app()->user->checkAccess('canSaveMedcardMovement')) { ?>
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
    <?php } ?>
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