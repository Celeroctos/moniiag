<div class="navbar navbar-blue navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><?php echo Yii::app()->name ?></a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li><a href="#">Справка</a></li>
            </ul>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'login-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/users/login'),
                'htmlOptions' => array(
                    'class' => 'navbar-form navbar-right',
                    'role' => 'form'
                )
            ));
            ?>
                <div class="form-group">
                    <?php echo $form->textField($loginFormModel,'login', array(
                        'id' => 'login',
                        'class' => 'form-control',
                        'placeholder' => 'Логин'
                    )); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->passwordField($loginFormModel,'password', array(
                        'id' => 'password',
                        'class' => 'form-control',
                        'placeholder' => 'Пароль'
                    )); ?>
                </div>
                <?php echo CHtml::ajaxSubmitButton(
                    'Войти',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/users/login'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                        $("#login-form").trigger("success", [data, textStatus, jqXHR])
                                    }'
                    ),
                    array(
                        'class' => 'btn btn-success'
                    )
                ); ?>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="loginSuccessPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Успешно!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Поздравляем, вы успешно вошли в систему. Вы можете перейти к <?php echo CHtml::link('редактированию своего профиля', array('/settings/profile/view')) ?> или продолжить работу с системой, закрыв данное окно.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="loginErrorPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Ошибка!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Вы не ввели логин и / или пароль!</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="loginNotFoundPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Пользователь не найден!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Пользователь с таким логином и паролем не существует в базе.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="pop-keyboard-help">
    <div class="pop-inner">
        <h5><strong>Управление с клавиатуры для текущего положения:</strong></h5>
        <div class="keyslist">

        </div>
    </div>
</div>