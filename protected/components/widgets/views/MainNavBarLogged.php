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
                <li><a href="#">Об авторах</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right" id="loggedUserNavbar">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Здравствуйте, <strong><?php echo $userName; ?>!</strong> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#">Редактирование профиля</a>
                        </li>
                        <li>
                            <a href="#">Просмотр пациентов</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'logout-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/users/logout'),
                'htmlOptions' => array(
                    'class' => 'navbar-form navbar-right',
                    'role' => 'form'
                )
            ));
            ?>

                <?php echo CHtml::ajaxSubmitButton(
                    'Выйти',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/users/logout'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                        $("#logout-form").trigger("success", [data, textStatus, jqXHR])
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