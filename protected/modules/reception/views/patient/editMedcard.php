<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/767e5633/jquery.yiiactiveform.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/searchAddPatient.js" ></script>
<?php if(Yii::app()->user->checkAccess('editMedcard')) { ?>
<h4>Редактирование карты пациента</h4>
<p class="text-left">
    Заполните поля формы, чтобы отредактировать карту существующего пациента <span class="text-danger bold">(<?php echo $fio; ?>, полис №<?php echo $policy_number; ?>, карта №<?php echo $card_number; ?>)</span>
</p>
<?php } else { ?>
<h4>Информация о карте пациента</h4>
<p class="text-left">
    Информация о карте пациента <span class="text-danger bold">(<?php echo $fio; ?>, полис №<?php echo $policy_number; ?>, карта №<?php echo $card_number; ?>)</span>:
</p>
<?php } ?>
<div class="row default-padding">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'patient-medcard-edit-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/editcard'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
        <div class="row">
            <div class="col-xs-8">
                <?php echo $form->hiddenField($model,'cardNumber', array(
                    'id' => 'cardNumber',
                    'class' => 'form-control'
                )); ?>
                <?php
                $this->widget('application.modules.reception.components.widgets.MedcardFormWidget', array(
                    'form' => $form,
                    'model' => $model,
                    'privilegesList' => $privilegesList
                ));
                ?>
            </div>
        </div>
        <?php if(Yii::app()->user->checkAccess('editMedcard')) { ?>
        <div class="form-group">
            <div class="add-patient-submit">
                <?php echo CHtml::ajaxSubmitButton(
                    'Редактировать',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/editcard'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                    $("#patient-medcard-edit-form").trigger("success", [data, textStatus, jqXHR])
                                }'
                    ),
                    array(
                        'class' => 'btn btn-success'
                    )
                ); ?>
            </div>
        </div>
        <?php } ?>
    <?php $this->endWidget(); ?>
</div>
<div class="modal fade error-popup" id="errorAddPopup">
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
<div class="modal fade error-popup" id="successAddPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Успешно!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Вы успешно отредактировали карту.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>