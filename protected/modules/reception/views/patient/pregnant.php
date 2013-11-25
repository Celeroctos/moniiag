<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/767e5633/jquery.yiiactiveform.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/pregnant.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<h4>Поиск беременной по ОМС</h4>
<div class="row">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'pregnant-search-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/searchpregnant'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
    <div class="form-group">
        <?php echo $form->labelEx($model,'omsNumber', array(
            'class' => 'col-xs-2 control-label'
        )); ?>
        <div class="col-xs-4">
            <?php echo $form->textField($model,'omsNumber', array(
                'id' => 'omsNumber',
                'class' => 'form-control',
                'placeholder' => 'ОМС'
            )); ?>
            <?php echo $form->error($model,'omsNumber'); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo $form->labelEx($model,'lastName', array(
            'class' => 'col-xs-2 control-label'
        )); ?>
        <div class="col-xs-4">
            <?php echo $form->textField($model,'lastName', array(
                'id' => 'lastName',
                'class' => 'form-control',
                'placeholder' => 'Фамилия'
            )); ?>
            <?php echo $form->error($model,'lastName'); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo $form->labelEx($model,'firstName', array(
            'class' => 'col-xs-2 control-label'
        )); ?>
        <div class="col-xs-4">
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
            'class' => 'col-xs-2 control-label'
        )); ?>
        <div class="col-xs-4">
            <?php echo $form->textField($model,'middleName', array(
                'id' => 'middletName',
                'class' => 'form-control',
                'placeholder' => 'Отчество'
            )); ?>
            <?php echo $form->error($model,'middleName'); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo $form->labelEx($model,'cardNumber', array(
            'class' => 'col-xs-2 control-label'
        )); ?>
        <div class="col-xs-4">
            <?php echo $form->textField($model,'cardNumber', array(
                'id' => 'cardNumber',
                'class' => 'form-control',
                'placeholder' => 'Номер карты'
            )); ?>
            <?php echo $form->error($model,'cardNumber'); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="pregnant-search-submit">
            <?php echo CHtml::ajaxSubmitButton(
                'Поиск',
                CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/searchpregnant'),
                array(
                    'success' => 'function(data, textStatus, jqXHR) {
                                    $("#pregnant-search-form").trigger("success", [data, textStatus, jqXHR])
                                }'
                ),
                array(
                    'class' => 'btn btn-success'
                )
            ); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>
<div class="col-xs-5">
    <div class="row <?php echo ($model->cardNumber == null) ? 'no-display' : ''; ?>" id="pregnantCont">
        <h5>Найденные пациентки:</h5>
        <div class="col-xs-12 borderedBox">
            <table class="table table-condensed table-hover" id="omsSearchPregnantResult">
                <thead>
                <tr class="header">
                    <td>
                        ФИО
                    </td>
                    <td>
                        Номер ОМС
                    </td>
                    <td>
                        Номер карты
                    </td>
                    <td>
                        Поставить на учёт
                    </td>
                </tr>
                </thead>
                <tbody>
                <?php if($model->id != null) { ?>
                    <tr>
                        <td>
                            <a href="#" title="Посмотреть информацию по пациентке">
                                <?php echo "{$model->lastName} {$model->firstName} {$model->middleName}"; ?>
                            </a>
                        </td>
                        <td>
                            <a href="http://moniiag.toonftp.ru/index.php/reception/patient/editomsview/?omsid=<?php echo "{$model->id}"; ?>">
                                <?php echo "{$model->omsNumber}"; ?>
                            </a>
                        </td>
                        <td>
                            <a href="http://moniiag.toonftp.ru/index.php/reception/patient/editcardview/?cardid=<?php echo "{$model->cardNumber}"; ?>">
                                <?php echo "{$model->cardNumber}"; ?>
                            </a>
                        </td>
                        <td>
                            <a href="http://moniiag.toonftp.ru/index.php/reception/patient/addpregnant/?cardid=<?php echo $model->cardNumber; ?>">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if($modelAddEdit->cardId != null) { ?>
<div class="col-xs-7">
    <div class="row">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'pregnant-addedit-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/searchpregnant'),
            'htmlOptions' => array(
                'class' => 'form-horizontal col-xs-12',
                'role' => 'form'
            )
        ));
        ?>
        <h4>Информация о беременной</h4>
        <p>Заполните (или исправьте при необходимости) поля, чтобы поставить пациентку на учёт к врачу.</p>
        <?php echo $form->hiddenField($modelAddEdit, 'id', array(
            'id' => 'id',
            'class' => 'form-control'
        )); ?>
        <?php echo $form->hiddenField($modelAddEdit, 'cardId', array(
            'id' => 'cardId',
            'class' => 'form-control'
        )); ?>
        <div class="form-group">
            <?php echo $form->labelEx($modelAddEdit, 'registerType', array(
                'class' => 'col-xs-4 control-label'
            )); ?>
            <div class="col-xs-8">
                <?php echo $form->dropDownList($modelAddEdit, 'registerType', array('Пришла вообще первый раз', 'Пришла первый раз в этом году', 'Пришла первый раз по этому случаю'), array(
                    'id' => 'registerType',
                    'class' => 'form-control'
                )); ?>
                <?php echo $form->error($modelAddEdit,'registerType'); ?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($modelAddEdit,'doctorId', array(
                'class' => 'col-xs-4 control-label'
            )); ?>
            <div class="col-xs-8">
                <?php echo $form->dropDownList($modelAddEdit, 'doctorId', $doctorsList, array(
                    'id' => 'doctorId',
                    'class' => 'form-control'
                )); ?>
                <?php echo $form->error($modelAddEdit,'doctorId'); ?>
            </div>
        </div>
        <div class="form-group">
            <div class="pregnant-addedit-submit">
                <?php echo CHtml::ajaxSubmitButton(
                    'Редактировать',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/addeditpregnant'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                    $("#pregnant-addedit-form").trigger("success", [data, textStatus, jqXHR])
                                }'
                    ),
                    array(
                        'class' => 'btn btn-success'
                    )
                ); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<?php } ?>
<div class="modal fade error-popup" id="errorSearchPopup">
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
<div class="modal fade error-popup" id="successAddEditPregnantPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Успешно!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Запись успешно изменена.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="notFoundPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Сообщение</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>По введённым поисковым критериям не найдено ни одной пациентки.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>