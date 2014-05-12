<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/767e5633/jquery.yiiactiveform.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/searchAddPatient.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js" ></script>
<?php if(Yii::app()->user->checkAccess('addPatient')) { ?>
<h4>Первичная регистрация пациента (<?php echo $regPoint; ?> год)</h4>
<div class="row default-padding">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'patient-withoutcard-form',
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
        <div class="col-xs-5">
            <p>Данные по полису ОМС:</p>
            <?php
            $this->widget('application.modules.reception.components.widgets.OmsFormWidget', array(
                'form' => $form,
                'model' => $model
            )); ?>
        </div>
        <div class="col-xs-7">
            <p>Данные медицинской карты:</p>
            <?php $this->widget('application.modules.reception.components.widgets.MedcardFormWidget', array(
                'form' => $form,
                'model' => $model,
                'privilegesList' => $privilegesList,
                'showEditIcon' => 1,
                'template' => 'application.modules.reception.components.widgets.views.MedcardFormWidget'
            )); ?>
            <div class="form-group">
                <div class="add-patient-submit">
                    <?php echo CHtml::ajaxSubmitButton(
                        'Добавить',
                        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/add'),
                        array(
                            'success' => 'function(data, textStatus, jqXHR) {
                                    $("#patient-withoutcard-form").trigger("success", [data, textStatus, jqXHR])
                            }',
							'beforeSend' => 'function(jqXHR, settings) {
								 $(".add-patient-submit input").trigger("begin")
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
<?php } ?>
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
                    <p>Поздравляем, вы успешно добавили нового пациента и создали для него первую карту.</p>
                    <p>Номер карты: <span id="successCardNumber"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="printPatientBtn">Печать титульного листа</button>
                <button type="button" class="btn btn-success" id="writePatientBtn">Записать пациента</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<?php $this->widget('application.modules.reception.components.widgets.MedcardFormWidget', array(
    'form' => $form,
    'model' => $model,
    'privilegesList' => $privilegesList,
    'showEditIcon' => 1,
    'template' => 'application.modules.reception.components.widgets.views.addressEditPopup'
)); ?>