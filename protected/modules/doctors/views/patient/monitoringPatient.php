<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jqGrid/src/i18n/grid.locale-ru.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jqGrid/js/jquery.jqGrid.src.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/doctors/monitoringPatients.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/chooser.js"></script>

<!-- Поключаем plot -->
<!--script language="javascript" type="text/javascript" src="/assets/libs/plot/jquery.min.js"></script-->
<script language="javascript" type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/plot/jquery.jqplot.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/plot/plugins/jqplot.dateAxisRenderer.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/plot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/plot/plugins/jqplot.canvasTextRenderer.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/plot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<!-- -->

<h4>Мониторинг</h4>

<div class="col-xs-6">
    <table id="patientsOnMonitor"></table>
    <div id="patientsOnMonitorPager"></div>

     </div>
<div class="col-xs-6">
    <div id="chart2">

    </div>
</div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addMonitor">Добавить мониторинг</button>
    <button type="button" class="btn btn-default" id="editMonitor">Редактировать мониторинг</button>
    <button type="button" class="btn btn-default" id="deleteMonitor">Удалить мониторинг</button>
</div>

<!-- Дальше идут формы добавления/изменения мониторинга -->
<div class="modal fade" id="addMonitoringPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить мониторинг</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'shortName'),
                'id' => 'monitoring-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/enterprises/add'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- Пациент -->
                        <div class="form-group chooser" id="monPatientChooser">
                            <label for="doctor" class="col-xs-3 control-label">Пациент: </label>

                            <div class="col-xs-9">
                                <input type="text" class="form-control" id="doctor"
                                       placeholder="Начинайте вводить...">
                                <ul class="variants no-display">
                                </ul>
                                <div class="choosed">
                                </div>
                            </div>
                        </div>
                        <!-- Тип мониторирования -->
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'monType', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-5">
                                <?php echo $form->dropDownList($model, 'monType', $monitoringTypes, array(
                                    'id' => 'monType',
                                    'class' => 'form-control'
                                )); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <!--<button type="button" class="btn btn-primary">Добавить</button>-->
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/enterprises/add'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#monitoring-add-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade" id="editMonitoringPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать мониторинг</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'shortName'),
                'id' => 'monitoring-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/enterprises/edit'),
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

                            <div class="form-group chooser" id="monPatientChooser">
                                <label for="doctor" class="col-xs-3 control-label">Пациент: </label>

                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="doctor"
                                           placeholder="Начинайте вводить...">
                                    <ul class="variants no-display">
                                    </ul>
                                    <div class="choosed">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo $form->labelEx($model,'monType', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-5">
                                    <?php echo $form->dropDownList($model, 'monType', $monitoringTypes, array(
                                        'id' => 'monType',
                                        'class' => 'form-control'
                                    )); ?>
                                </div>
                            </div>


                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/enterprises/edit'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#monitoring-edit-form").trigger("success", [data, textStatus, jqXHR])
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