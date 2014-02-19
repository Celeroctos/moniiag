<?php $this->widget('application.components.widgets.AdminUsersTabMenu') ?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/users.js"></script>
<table id="users"></table>
<div id="usersPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addUser">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editUser">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default" id="editPasswordUser">Сменить пароль у выбранного</button>
    <button type="button" class="btn btn-default" id="deleteUser">Удалить выбранные</button>
</div>
<div class="modal fade" id="addUserPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить пользователя</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'userName'),
                'id' => 'user-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/users/add'),
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
                            <?php echo $form->labelEx($model,'username', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'username', array(
                                    'id' => 'username',
                                    'class' => 'form-control',
                                    'placeholder' => 'Отображаемое имя'
                                )); ?>
                                <?php echo $form->error($model,'username'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'login', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'login', array(
                                    'id' => 'login',
                                    'class' => 'form-control',
                                    'placeholder' => 'Логин'
                                )); ?>
                                <?php echo $form->error($model,'login'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'password', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->passwordField($model,'password', array(
                                    'id' => 'password',
                                    'class' => 'form-control',
                                    'placeholder' => 'Пароль'
                                )); ?>
                                <?php echo $form->error($model,'password'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'roleId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'roleId', $rolesList, array(
                                    'id' => 'roleId',
                                    'class' => 'form-control roleChooseCombo',
                                    'multiple' => 'multiple'
                                )); ?>
                                <?php echo $form->error($model,'roleId'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'employeeId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'employeeId', $employeesList, array(
                                    'id' => 'employeeId',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'employeeId'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/users/add'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#user-add-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade" id="editUserPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать пользователя</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'shortName'),
                'id' => 'user-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/users/edit'),
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
                            <?php echo $form->labelEx($model,'username', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'username', array(
                                    'id' => 'username',
                                    'class' => 'form-control',
                                    'placeholder' => 'Отображаемое имя'
                                )); ?>
                                <?php echo $form->error($model,'username'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'login', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'login', array(
                                    'id' => 'login',
                                    'class' => 'form-control',
                                    'placeholder' => 'Логин'
                                )); ?>
                                <?php echo $form->error($model,'login'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'roleId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'roleId', $rolesList, array(
                                    'id' => 'roleId',
                                    'class' => 'form-control roleChooseCombo',
                                    'multiple' => 'multiple'
                                )); ?>
                                <?php echo $form->error($model,'roleId'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'employeeId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'employeeId', $employeesList, array(
                                    'id' => 'employeeId',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'employeeId'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Отредактировать',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/users/edit'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#user-edit-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade" id="editUserPasswordPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Сменить пароль пользователя</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'password'),
                'id' => 'user-edit-password-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/users/changepass'),
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
                            <?php echo $form->labelEx($model,'password', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->passwordField($model,'password', array(
                                    'id' => 'username',
                                    'class' => 'form-control',
                                    'placeholder' => 'Пароль'
                                )); ?>
                                <?php echo $form->error($model,'password'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'passwordRepeat', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->passwordField($model,'passwordRepeat', array(
                                    'id' => 'passwordRepeat',
                                    'class' => 'form-control',
                                    'placeholder' => 'Повтор пароля'
                                )); ?>
                                <?php echo $form->error($model,'passwordRepeat'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Отредактировать',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/users/changepass'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#user-edit-password-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade error-popup" id="errorAddUserPopup">
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