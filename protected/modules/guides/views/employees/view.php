<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/employees.js"></script>
<table id="employees"></table>
<div id="employeesPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addEmployee">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editEmployee">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default" id="deleteEmployee">Удалить выбранные</button>
</div>
<div class="modal fade" id="addEmployeePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить учреждение</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'shortName'),
                'id' => 'employee-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/employees/add'),
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
                            <?php echo $form->labelEx($model,'firstName', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'firstName', array(
                                    'id' => 'firstName',
                                    'class' => 'form-control',
                                    'placeholder' => 'Имя'
                                )); ?>
                                <?php echo $form->error($model,'firstName'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'middleName', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'middleName', array(
                                    'id' => 'middleName',
                                    'class' => 'form-control',
                                    'placeholder' => 'Отчество'
                                )); ?>
                                <?php echo $form->error($model,'middleName'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'lastName', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'lastName', array(
                                    'id' => 'lastName',
                                    'class' => 'form-control',
                                    'placeholder' => 'Фамилия'
                                )); ?>
                                <?php echo $form->error($model,'lastName'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'postId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'postId', $postsList, array(
                                    'id' => 'postId',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'postId'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'tabelNumber', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'tabelNumber', array(
                                    'id' => 'tabelNumber',
                                    'class' => 'form-control',
                                    'placeholder' => 'Табельный номер'
                                )); ?>
                                <?php echo $form->error($model,'tabelNumber'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'contactCode', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'contactCode', $contactCodesList, array(
                                    'id' => 'contactCode',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'contactCode'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'degreeId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'degreeId', $degreesList, array(
                                    'id' => 'degreeId',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'degreeId'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'titulId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'titulId', $titulsList, array(
                                    'id' => 'titulId',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'titulId'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'dateBegin', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9 input-group date" id="dateBegin-cont">
                                <?php echo $form->textField($model,'dateBegin', array(
                                    'id' => 'dateBegin',
                                    'class' => 'form-control',
                                    'placeholder' => 'Дата начала действия'
                                )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'dateEnd', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9 input-group date" id="dateEnd-cont">
                                <?php echo $form->textField($model,'dateEnd', array(
                                    'id' => 'dateEnd',
                                    'class' => 'form-control',
                                    'placeholder' => 'Дата окончания действия'
                                )); ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'wardCode', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'wardCode', $wardsList, array(
                                    'id' => 'wardCode',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'wardCode'); ?>
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
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/employees/add'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#employee-add-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade error-popup" id="errorAddEmployeePopup">
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
