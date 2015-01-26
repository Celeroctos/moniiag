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
                <li><a href="<?php echo Yii::app()->request->baseUrl; ?>/uploads/files/userguide.pdf" target="_blank">Справка</a></li>
            </ul>
            <div class="row" id="dateInfoCont">
                Сегодня <?php echo $weekdayDesc; ?>, <br /> <?php echo $day; ?> <?php echo $monthDesc; ?> <?php echo $year; ?> года, <?php echo $time; ?>
            </div>
            <ul class="nav navbar-nav alarm-button" style="width:50px;height:50px;">
                <li><img src="/images/icons/alarm.png" width="50" height="50" class="no-display"></img></li>
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
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/users/logout'),
                'htmlOptions' => array(
                    'class' => 'navbar-form navbar-right',
                    'role' => 'form'
                )
            ));
            ?>

                <?php echo CHtml::ajaxSubmitButton(
                    'Выйти',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/users/logout'),
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
            <div class="navbar-right font-panel">
                <button id="fontPlus" class="btn btn-success" title="Увеличить размер шрифта">+</button>
                <span class="sampleLetterSize">Шрифт - <?php echo Yii::app()->user->fontSize; ?></span>
                <button id="fontMinus" class="btn btn-success" title="Уменьшить размер шрифта">-</button>
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