<div class="navbar-fixed-top">
    <div id="recycleBin-cont">
        <img src="/images/icons/bin.jpg" class="recycleBin" alt="" width="40" height="40" title="Перенеся значок с панели в корзину, вы удалите его с панели быстрого доступа"/>
    </div>
    <!--<div id="quickPanel">
        <?php $this->widget('application.components.widgets.QuickPanelListWidget'); ?>
    </div>
    <div id="quickPanelArrow">
        <span class="glyphicon glyphicon-collapse-down"></span>
    </div>-->
</div>
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
                <li><a href="#" class="keyboard-help-link" data-toggle="keyboard-help">Помощь по клавиатуре</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right" id="loggedUserNavbar">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><strong><?php echo isset(Yii::app()->user->fio) ? Yii::app()->user->fio : ''; ?></strong> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <?php echo CHtml::link('Редактирование профиля', array('/settings/profile/view')); ?>
                        </li>
                        <!--<li>
                            <?php echo CHtml::link('Просмотр пациентов', array('/doctors/shedule/view')); ?>
                        </li>-->
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
<div class="pop-keyboard-help">
    <div class="pop-inner">
        <h5><strong>Управление с клавиатуры для текущего положения:</strong></h5>
        <div class="keyslist">
        </div>
    </div>
</div>
