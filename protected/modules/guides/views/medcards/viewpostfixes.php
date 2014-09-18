<h4>Правила формирования номеров медицинских карт</h4>
<?php $this->widget('application.modules.guides.components.widgets.MedcardsTabMenu', array(
));
?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/medcardsPostfixes.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript">
    globalVariables.guideEdit = '<?php echo Yii::app()->user->checkAccess('guideEditPrivelege'); ?>';
</script>
<table id="postfixes"></table>
<div id="postfixesPager"></div>
<div class="btn-group default-margin-top">
    <?php if(Yii::app()->user->checkAccess('guideAddMedcardPostfix')) { ?>
        <button type="button" class="btn btn-default" id="addPostfix">Добавить запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideEditMedcardPostfix')) { ?>
        <button type="button" class="btn btn-default" id="editPostfix">Редактировать выбранную запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideDeleteMedcardPostfix')) { ?>
        <button type="button" class="btn btn-default" id="deletePostfix">Удалить запись</button>
    <?php } ?>
</div>
<div class="modal fade" id="editPostfixPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать постфикс</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'postfix-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/medcards/editpostfix'),
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
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/medcards/editpostfix'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#postfix-edit-form").trigger("success", [data, textStatus, jqXHR])
                        }',
                        'beforeSend' => 'function(jqXHR, settings) {
                             $("#postfix-edit-form").trigger("beforesend", [settings, jqXHR])
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
<div class="modal fade" id="addPostfixPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить постфикс</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'postfix-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/medcards/addpostfix'),
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
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/medcards/addpostfix'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#postfix-add-form").trigger("success", [data, textStatus, jqXHR])
                        }',
                        'beforeSend' => 'function(jqXHR, settings) {
                             $("#postfix-add-form").trigger("beforesend", [settings, jqXHR])
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