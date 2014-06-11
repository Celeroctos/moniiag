<?php if ($this->previewMode) {?>
	<script type="text/javascript">
		    if (globalVariables.elementsDependences==undefined)		    
		    {
					globalVariables.elementsDependences = new Array();
		    }
	</script>
    <?php } ?>
<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'template-edit-form',
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
    echo $form->hiddenField($model,'templateName', array(
    'id' => 'templateName',
    'class' => 'form-control',
    'value' => $templateName
    ));
    echo $form->hiddenField($model,'templateId', array(
    'id' => 'templateId',
    'class' => 'form-control',
    'value' => $templateId
    )); ?>

<?php if(!$this->previewMode && $this->templateType == 0) { ?>
<div <?php echo !$isActiveTemplate ? 'class="no-display"' : ''; ?> id="tab<?php echo $templateId; ?>">
<?php } ?>
    <?php

    foreach($categories  as $index => $template) {
        foreach($template['cats']  as $key => $categorie) {


            $this->drawCategorie($categorie, $form, $model, $lettersInPixel, $templatePrefix);
        }
    } ?>
<?php if(!$this->previewMode && $this->templateType == 0) { ?>
</div>
<?php } ?>
<?php if(!$withoutSave && Yii::app()->user->checkAccess('canSaveMedcardMovement')) { ?>
<div class="form-group submitEditPatient">
	<?php echo CHtml::ajaxSubmitButton(
		'Сохранить',
		CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/doctors/shedule/patientedit'),
		array(
			'success' => 'function(data, textStatus, jqXHR) {
                $("#template-edit-form").trigger("success", [data, textStatus, jqXHR])
            }'
		),
		array(
				'class' => 'templateContentSave'
		)
	); ?>
</div>

<?php } ?>
<?php
$this->endWidget();
?>