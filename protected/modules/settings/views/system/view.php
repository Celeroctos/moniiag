<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/767e5633/jquery.yiiactiveform.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/settings/system.js"></script>
<h4>Системные настройки</h4>
<div class="row" id="system-settings-block">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'focus' => array($model,'lettersInPixel'),
        'id' => 'system-settings-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/settings/system/settingsedit'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
    <div class="form-group">
        <?php echo $form->labelEx($model,'lettersInPixel', array(
            'class' => 'col-xs-2 control-label'
        )); ?>
        <div class="col-xs-4">
            <?php echo $form->textField($model,'lettersInPixel', array(
                'id' => 'letterInPixel',
                'class' => 'form-control',
                'placeholder' => 'Количество пикселей в символе'
            )); ?>
            <?php echo $form->error($model,'lettersInPixel'); ?>
        </div>
    </div>
	<div class="form-group">
		<?php echo $form->labelEx($model,'tasuMode', array(
			'class' => 'col-xs-2 control-label'
		)); ?>
		<div class="col-xs-4">
			<?php
			echo $form->dropDownList($model, 'tasuMode', array('Да','Нет'), array(
				'id' => 'tasuMode',
				'class' => 'form-control'
			)); 
			?>
		</div>
	</div>
	<div class="form-group">
        <?php echo $form->labelEx($model,'sessionStandbyTime', array(
            'class' => 'col-xs-2 control-label'
        )); ?>
        <div class="col-xs-4">
            <?php echo $form->textField($model,'sessionStandbyTime', array(
                'id' => 'sessionStandbyTime',
                'class' => 'form-control',
                'placeholder' => 'Время простоя сессии пользователя'
            )); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo CHtml::ajaxSubmitButton(
            'Сохранить настройки',
            CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/settings/system/settingsedit'),
            array(
                'success' => 'function(data, textStatus, jqXHR) {
                                    $("#system-settings-form").trigger("success", [data, textStatus, jqXHR])
                                }'
            ),
            array(
                'class' => 'btn btn-success'
            )
        ); ?>
    </div>
    <?php $this->endWidget(); ?>
</div>
<h4>Обслуживание</h4>
<div class="row">
    <div class="form-group">
        <?php echo CHtml::button(
            'Очистить таблицы для приёмов пациентов',
            array(
                'class' => 'btn btn-success',
                'id' => 'clearGreetingDataSubmit'
            )
        ); ?>
    </div>
</div>
<div class="modal fade error-popup" id="successSystemSettingsEditPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Успешно!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Настройки системы сохранены.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="deleteCreetingConfirm">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Внимание!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Нажатие этой кнопки приведёт к полному удалению информации о приёмах! Эта возможность нужна только разработчикам. Если Вы таковым не являетесь - нажмите "Отмена"</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal" id="allGreetingsDelete">Удалить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancelDeleteAllGreetings">Отмена</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="successPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Успешно!</h4>
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
<div class="modal fade error-popup" id="errorSystemSettingsEditPopup">
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