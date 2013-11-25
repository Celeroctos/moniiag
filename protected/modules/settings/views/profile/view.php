<h4>Редактирование профиля</h4>
<p>Здесь можно отредактировать Ваш профиль пользователя.</p>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/settings/profile.js" ></script>
<div class="row default-padding">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'edit-profile-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/add'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
    <div class="row">
        <div class="form-group">
            <?php echo $form->labelEx($avatarModel,'avatar', array(
                'class' => 'col-xs-2 control-label'
            )); ?>
            <div class="col-xs-4">
                <?php echo $form->fileField($avatarModel,'avatar', array(
                    'id' => 'avatar'
                )); ?>
                <?php echo $form->error($avatarModel,'avatar'); ?>
                <p class="help-block">Ваш файл должен быть не более 130 x 130 пикселей и 50KB. Файл может быть в формате *.jpg, *.png или *.gif.</p>
            </div>
        </div>
        <div class="form-group">
            <div class="edit-profile-submit">
                <?php echo CHtml::ajaxSubmitButton(
                    'Загрузить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/settings/profile/avatarupload'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                        $("#edit-profile-form").trigger("success", [data, textStatus, jqXHR])
                                    }'
                    ),
                    array(
                        'class' => 'btn btn-success'
                    )
                ); ?>
            </div>
        </div>
    </div>
    <?php $this->endWidget(); ?>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'edit-profile-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/add'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
    <div class="row">
        <div class="form-group">
            <?php echo $form->labelEx($model,'username', array(
                'class' => 'col-xs-2 control-label'
            )); ?>
            <div class="col-xs-4">
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
                'class' => 'col-xs-2 control-label'
            )); ?>
            <div class="col-xs-4">
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
                'class' => 'col-xs-2 control-label'
            )); ?>
            <div class="col-xs-4">
                <?php echo $form->passwordField($model,'password', array(
                    'id' => 'password',
                    'class' => 'form-control',
                    'placeholder' => 'Пароль'
                )); ?>
                <?php echo $form->error($model,'password'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($model,'passwordRepeat', array(
                'class' => 'col-xs-2 control-label'
            )); ?>
            <div class="col-xs-4">
                <?php echo $form->passwordField($model,'passwordRepeat', array(
                    'id' => 'passwordRepeat',
                    'class' => 'form-control',
                    'placeholder' => 'Повтор пароля'
                )); ?>
                <?php echo $form->error($model,'passwordRepeat'); ?>
            </div>
        </div>
        <div class="form-group">
            <div class="edit-profile-submit">
                <?php echo CHtml::ajaxSubmitButton(
                    'Редактировать',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/settings/profile/edit'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                        $("#edit-profile-form").trigger("success", [data, textStatus, jqXHR])
                                    }'
                    ),
                    array(
                        'class' => 'btn btn-success'
                    )
                ); ?>
            </div>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>
<div class="modal fade error-popup" id="errorProflePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Ошибка!</h4>
            </div>
            <div class="modal-body">
                <div class="row">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="successProfilePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Успешно!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Профиль отредактирован.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>