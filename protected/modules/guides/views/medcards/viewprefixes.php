<h4>Правила формирования номеров медицинских карт</h4>
<?php $this->widget('application.modules.guides.components.widgets.MedcardsTabMenu', array(
));
?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/medcardsPrefixes.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript">
    globalVariables.guideEdit = '<?php echo Yii::app()->user->checkAccess('guideEditPrivelege'); ?>';
</script>
<table id="prefixes"></table>
<div id="prefixesPager"></div>
<div class="btn-group default-margin-top">
    <?php if(Yii::app()->user->checkAccess('guideAddMedcardPrefix')) { ?>
        <button type="button" class="btn btn-default" id="addPrefix">Добавить запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideEditMedcardPrefix')) { ?>
        <button type="button" class="btn btn-default" id="editPrefix">Редактировать выбранную запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideDeleteMedcardPrefix')) { ?>
        <button type="button" class="btn btn-default" id="deletePrefix">Удалить запись</button>
    <?php } ?>
</div>
<div class="modal fade" id="editPrefixPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать префикс</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'prefix-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/medcards/editprefix'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
            <div class="modal-body">
                <div class="row">
					<?php echo $form->hiddenField($model,'id', array(
						'id' => 'id',
						'class' => 'form-control'
					)); ?>
					<div class="form-group">
						<?php echo $form->labelEx($model,'value', array(
							'class' => 'col-xs-3 control-label'
						)); ?>
						<div class="col-xs-9">
							<?php echo $form->textField($model,'value', array(
								'id' => 'value',
								'class' => 'form-control',
								'placeholder' => 'Значение'
							)); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/medcards/editprefix'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#prefix-edit-form").trigger("success", [data, textStatus, jqXHR])
                        }',
                        'beforeSend' => 'function(jqXHR, settings) {
                             $("#prefix-edit-form").trigger("beforesend", [settings, jqXHR])
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
<div class="modal fade" id="addPrefixPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить префикс</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'prefix-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/medcards/addprefix'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
            <div class="modal-body">
                <div class="row">
					<div class="form-group">
						<?php echo $form->labelEx($model,'value', array(
							'class' => 'col-xs-3 control-label'
						)); ?>
						<div class="col-xs-9">
							<?php echo $form->textField($model,'value', array(
								'id' => 'value',
								'class' => 'form-control',
								'placeholder' => 'Значение'
							)); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/medcards/addprefix'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#prefix-add-form").trigger("success", [data, textStatus, jqXHR])
                        }',
                        'beforeSend' => 'function(jqXHR, settings) {
                             $("#prefix-add-form").trigger("beforesend", [settings, jqXHR])
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