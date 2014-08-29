<?php $this->widget('application.components.widgets.AdminUsersTabMenu') ?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/roles.js"></script>
<table id="roles"></table>
<div id="rolesPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addRole">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editRole">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default" id="deleteRole">Удалить запись</button>
</div>
<div class="modal fade" id="addRolePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить роль</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'roleName'),
                'id' => 'role-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/roles/add'),
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
                                    'placeholder' => 'Название'
                                )); ?>
                                <?php echo $form->error($model,'name'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'parentId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'parentId', $rolesList, array(
                                    'id' => 'parentId',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'roleId'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'pageId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'pageId', $pagesList, array(
                                    'id' => 'pageId',
                                    'class' => 'form-control'
                                )); ?>
                            </div>
                        </div>
                        <h4>Права доступа</h4>
                        <?php foreach($actions as $key => $actionGroup) { ?>
                        <h5><strong><?php echo $key; ?></strong></h5>
                        <div class="form-group">
                            <?php foreach($actionGroup as $key2 => $action) { ?>
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="action<?php echo $key2; ?>" value="<?php echo $key2; ?>" name="action<?php echo $key2; ?>"> <?php echo $action; ?>
                                </label>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/roles/add'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#role-add-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade" id="editRolePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать роль</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'shortName'),
                'id' => 'role-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/roles/edit'),
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
                            <?php echo $form->labelEx($model,'name', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'name', array(
                                    'id' => 'name',
                                    'class' => 'form-control',
                                    'placeholder' => 'Название'
                                )); ?>
                                <?php echo $form->error($model,'name'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'parentId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'parentId', $rolesList, array(
                                    'id' => 'parentId',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'roleId'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'pageId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'pageId', $pagesList, array(
                                    'id' => 'pageId',
                                    'class' => 'form-control'
                                )); ?>
                            </div>
                        </div>
                        <h4>Права доступа:</h4>
                        <?php foreach($actions as $key => $actionGroup) { ?>
                            <h5><strong><?php echo $key; ?></strong></h5>
                            <div class="form-group">
                                <?php foreach($actionGroup as $key2 => $action) { ?>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" id="action<?php echo $key2; ?>" value="<?php echo $key2; ?>" name="action<?php echo $key2; ?>" /> <?php echo $action; ?>
                                    </label>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/roles/edit'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#role-edit-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade error-popup" id="errorAddRolePopup">
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