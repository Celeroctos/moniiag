<h4>Правила формирования номеров медицинских карт</h4>
<?php $this->widget('application.modules.guides.components.widgets.MedcardsTabMenu', array(
));
?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/medcardsRules.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript">
    globalVariables.guideEdit = '<?php echo Yii::app()->user->checkAccess('guideEditPrivelege'); ?>';
</script>
<table id="rules"></table>
<div id="rulesPager"></div>
<div class="btn-group default-margin-top">
    <?php if(Yii::app()->user->checkAccess('guideAddMedcardRule')) { ?>
        <button type="button" class="btn btn-default" id="addRule">Добавить запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideEditMedcardRule')) { ?>
        <button type="button" class="btn btn-default" id="editRule">Редактировать выбранную запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideDeleteMedcardRule')) { ?>
        <button type="button" class="btn btn-default" id="deleteRule">Удалить запись</button>
    <?php } ?>
</div>
<div class="modal fade" id="editRulePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать правило</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'rule-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/medcards/editrule'),
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
						<?php echo $form->label($model,'name', array(
							'class' => 'col-xs-3 control-label text-left'
						)); ?>
						<div class="col-xs-9">
							<?php echo $form->textField($model, 'name', array(
								'id' => 'name',
								'class' => 'form-control'
							)); ?>
						</div>
					</div>
					<div class="form-group">
						<?php echo $form->label($model,'prefixId', array(
							'class' => 'col-xs-3 control-label text-left'
						)); ?>
						<div class="col-xs-9">
							<?php echo $form->dropDownList($model, 'prefixId', $prefixesList, array(
								'id' => 'prefixId',
								'class' => 'form-control'
							)); ?>
						</div>
					</div>
					<div class="form-group">
						<?php echo $form->label($model,'postfixId', array(
							'class' => 'col-xs-3 control-label text-left'
						)); ?>
						<div class="col-xs-9">
							<?php echo $form->dropDownList($model, 'postfixId', $postfixesList, array(
								'id' => 'postfixId',
								'class' => 'form-control'
							)); ?>
						</div>
					</div>
					<div class="form-group">
						<?php echo $form->label($model,'typeId', array(
							'class' => 'col-xs-3 control-label text-left'
						)); ?>
						<div class="col-xs-9">
							<?php echo $form->dropDownList($model, 'typeId', $typesList, array(
								'id' => 'typeId',
								'class' => 'form-control'
							)); ?>
						</div>
					</div>
					<div class="form-group">
						<?php echo $form->label($model,'parentId', array(
							'class' => 'col-xs-3 control-label text-left'
						)); ?>
						<div class="col-xs-9">
							<?php echo $form->dropDownList($model, 'parentId', $rulesList, array(
								'id' => 'parentId',
								'class' => 'form-control'
							)); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/medcards/editrule'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#rule-edit-form").trigger("success", [data, textStatus, jqXHR])
                        }',
                        'beforeSend' => 'function(jqXHR, settings) {
                             $("#rule-edit-form").trigger("beforesend", [settings, jqXHR])
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
<div class="modal fade" id="addRulePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить правило</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'rule-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/medcards/addrule'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
            <div class="modal-body">
                <div class="row">
					<div class="form-group">
						<?php echo $form->label($model,'name', array(
							'class' => 'col-xs-3 control-label text-left'
						)); ?>
						<div class="col-xs-9">
							<?php echo $form->textField($model, 'name', array(
								'id' => 'name',
								'class' => 'form-control'
							)); ?>
						</div>
					</div>
					<div class="form-group">
						<?php echo $form->label($model,'prefixId', array(
							'class' => 'col-xs-3 control-label text-left'
						)); ?>
						<div class="col-xs-9">
							<?php echo $form->dropDownList($model, 'prefixId', $prefixesList, array(
								'id' => 'prefixId',
								'class' => 'form-control'
							)); ?>
						</div>
					</div>
					<div class="form-group">
						<?php echo $form->label($model,'postfixId', array(
							'class' => 'col-xs-3 control-label text-left'
						)); ?>
						<div class="col-xs-9">
							<?php echo $form->dropDownList($model, 'postfixId', $postfixesList, array(
								'id' => 'postfixId',
								'class' => 'form-control'
							)); ?>
						</div>
					</div>
					<div class="form-group">
						<?php echo $form->label($model,'typeId', array(
							'class' => 'col-xs-3 control-label text-left'
						)); ?>
						<div class="col-xs-9">
							<?php echo $form->dropDownList($model, 'typeId', $typesList, array(
								'id' => 'typeId',
								'class' => 'form-control'
							)); ?>
						</div>
					</div>
					<div class="form-group no-display">
						<?php echo $form->label($model,'parentId', array(
							'class' => 'col-xs-3 control-label text-left'
						)); ?>
						<div class="col-xs-9">
							<?php echo $form->dropDownList($model, 'parentId', $rulesList, array(
								'id' => 'parentId',
								'class' => 'form-control'
							)); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/medcards/addrule'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#rule-add-form").trigger("success", [data, textStatus, jqXHR])
                        }',
                        'beforeSend' => 'function(jqXHR, settings) {
                             $("#rule-add-form").trigger("beforesend", [settings, jqXHR])
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