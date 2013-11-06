<div class="row">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'contact-filter-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/employees/filter'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
    <div class="form-group">
        <?php echo $form->label($modelFilter,'enterpriseCode', array(
            'class' => 'col-xs-2 control-label text-left'
        )); ?>
        <div class="col-xs-3">
            <?php echo $form->dropDownList($modelFilter, 'enterpriseCode', $enterprisesList, array(
                'id' => 'enterpriseCode',
                'class' => 'form-control',
                'options' => array('-1' => array('selected' => true))
            )); ?>
            <?php echo $form->error($modelFilter,'wardCode'); ?>
        </div>
    </div>
    <div class="form-group no-display">
        <?php echo $form->label($modelFilter,'wardCode', array(
            'class' => 'col-xs-2 control-label text-left'
        )); ?>
        <div class="col-xs-3">
            <?php echo $form->dropDownList($modelFilter, 'wardCode', $wardsList, array(
                'id' => 'wardCodeFilter',
                'class' => 'form-control',
                'options' => array('-1' => array('selected' => true))
            )); ?>
            <?php echo $form->error($modelFilter,'wardCode'); ?>
        </div>
    </div>
    <div class="form-group no-display">
        <?php echo $form->label($modelFilter,'employeeCode', array(
            'class' => 'col-xs-2 control-label text-left'
        )); ?>
        <div class="col-xs-3">
            <?php echo $form->dropDownList($modelFilter, 'employeeCode', $wardsList, array(
                'id' => 'employeeCodeFilter',
                'class' => 'form-control',
            )); ?>
            <?php echo $form->error($modelFilter,'employeeCode'); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo CHtml::ajaxSubmitButton(
            'Фильтровать',
            CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/contacts/filter'),
            array(
                'success' => 'function(data, textStatus, jqXHR) {
                                    $("#contact-filter-form").trigger("success", [data, textStatus, jqXHR])
                                }'
            ),
            array(
                'class' => 'btn btn-success'
            )
        ); ?>
    </div>
    <?php $this->endWidget(); ?>
</div>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/contacts.js"></script>
<table id="contacts"></table>
<div id="contactsPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addContact">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editContact">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default" id="deleteContact">Удалить выбранные</button>
</div>
<div class="modal fade" id="addContactPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить контакт</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'contact-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/contacts/add'),
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
                            <?php echo $form->labelEx($model,'type', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'type', $contactsTypesList, array(
                                    'id' => 'type',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'type'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'contactValue', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'contactValue', array(
                                    'id' => 'contactValue',
                                    'class' => 'form-control',
                                    'placeholder' => 'Значение контакта'
                                )); ?>
                                <?php echo $form->error($model,'contactValue'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/contacts/add'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#contact-add-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade" id="editContactPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать контакт</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'contact-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/contacts/edit'),
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
                            <?php echo $form->labelEx($model,'type', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'type', $contactsTypesList, array(
                                    'id' => 'type',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'type'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'contactValue', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'contactValue', array(
                                    'id' => 'contactValue',
                                    'class' => 'form-control',
                                    'placeholder' => 'Значение контакта'
                                )); ?>
                                <?php echo $form->error($model,'contactValue'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Отредактировать',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/contacts/edit'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#contact-edit-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade error-popup" id="errorAddContactPopup">
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
