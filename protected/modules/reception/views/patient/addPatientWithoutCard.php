<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/767e5633/jquery.yiiactiveform.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/searchAddPatient.js" ></script>
<h4>Первичная регистрация пациента и добавление первой ЭМК (<?php echo $regPoint; ?> год)</h4>
<p class="text-left">
    Не нашли в списке пациентов нужного? Добавьте запись о нём, заполнив поля формы.
</p>
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
            <div class="col-xs-6">
                <div class="form-group">
                    <?php echo $form->labelEx($model,'policy', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-5">
                        <?php echo $form->textField($model,'policy', array(
                            'id' => 'policy',
                            'class' => 'form-control',
                            'placeholder' => 'ОМС',
                            'autofocus'=>'1'
                            
                        )); ?>
                        <?php echo $form->error($model,'policy'); ?>
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
                    <?php echo $form->labelEx($model,'gender', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-5">
                        <?php echo $form->dropDownList($model, 'gender', array('Женский', 'Мужской'), array(
                            'id' => 'gender',
                            'class' => 'form-control'
                        )); ?>
                        <?php echo $form->error($model,'gender'); ?>
                    </div>
                </div>
                    <div class="form-group">
        <span class="col-xs-3">
        </span>           
        <div class="btn-group col-xs-5">
            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-plus glyphicon year-button up-year-button" >

            </button>
             <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-plus glyphicon month-button up-month-button">

            </button>
            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-plus glyphicon up-day-button">

            </button>
        </div>
    </div>               
                <div class="form-group">
                    <?php echo $form->labelEx($model,'birthday', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-6 input-group date" id="birthday-cont">
                        <?php echo $form->textField($model,'birthday', array(
                            'id' => 'birthday',
                            'class' => 'form-control',
                            'placeholder' => 'Формат гггг-мм-дд'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>     
                  <div  class="form-group">
        <span class="col-xs-3">
        </span>            
        <div class="btn-group col-xs-5">              
            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-minus glyphicon year-button down-year-button">

            </button>
             <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-minus glyphicon month-button down-month-button">

            </button>
            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-minus glyphicon down-day-button">

            </button>
        </div>
    </div>               
                <div class="form-group">
                    <?php echo $form->labelEx($model,'doctype', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-5">
                        <?php echo $form->dropDownList($model, 'doctype', array(1 => 'Паспорт'), array(
                            'id' => 'doctype',
                            'class' => 'form-control'
                        )); ?>
                        <?php echo $form->error($model,'doctype'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'serie', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-5">
                        <?php echo $form->textField($model,'serie', array(
                            'id' => 'serie',
                            'class' => 'form-control',
                            'placeholder' => 'Серия'
                        )); ?>
                        <?php echo $form->error($model,'serie'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'docnumber', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-6">
                        <?php echo $form->textField($model,'docnumber', array(
                            'id' => 'docnumber',
                            'class' => 'form-control',
                            'placeholder' => 'Номер'
                        )); ?>
                        <?php echo $form->error($model,'docnumber'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'whoGived', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-8">
                        <?php echo $form->textField($model,'whoGived', array(
                            'id' => 'whoGived',
                            'class' => 'form-control',
                            'placeholder' => 'Кем выдан'
                        )); ?>
                        <?php echo $form->error($model,'whoGived'); ?>
                    </div>
                </div>
                                    <div class="form-group">
        <span class="col-xs-3">
        </span>           
        <div class="btn-group col-xs-5">
            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-plus glyphicon year-button up-year-button" >

            </button>
             <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-plus glyphicon month-button up-month-button">

            </button>
            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-plus glyphicon up-day-button">

            </button>
        </div>
    </div>    
                <div class="form-group">
                    <?php echo $form->labelEx($model,'documentGivedate', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-6 input-group date" id="document-givedate-cont">
                        <?php echo $form->textField($model,'documentGivedate', array(
                            'id' => 'documentGivedate',
                            'class' => 'form-control',
                            'placeholder' => 'Формат гггг-мм-дд'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                
                                  <div  class="form-group">
        <span class="col-xs-3">
        </span>            
        <div class="btn-group col-xs-5">              
            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-minus glyphicon year-button down-year-button">

            </button>
             <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-minus glyphicon month-button down-month-button">

            </button>
            <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-minus glyphicon down-day-button">

            </button>
        </div>
    </div>   
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <?php echo $form->labelEx($model,'addressReg', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'addressReg', array(
                            'id' => 'addressReg',
                            'class' => 'form-control',
                            'placeholder' => 'Адрес регистрации'
                        )); ?>
                        <?php echo $form->error($model,'addressReg'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'address', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'address', array(
                            'id' => 'address',
                            'class' => 'form-control',
                            'placeholder' => 'Адрес проживания'
                        )); ?>
                        <?php echo $form->error($model,'address'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'workPlace', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'workPlace', array(
                            'id' => 'workPlace',
                            'class' => 'form-control',
                            'placeholder' => 'Место работы'
                        )); ?>
                        <?php echo $form->error($model,'workPlace'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'workAddress', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'workAddress', array(
                            'id' => 'workAddress',
                            'class' => 'form-control',
                            'placeholder' => 'Адрес работы'
                        )); ?>
                        <?php echo $form->error($model,'workAddress'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'post', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'post', array(
                            'id' => 'post',
                            'class' => 'form-control',
                            'placeholder' => 'Должность'
                        )); ?>
                        <?php echo $form->error($model,'post'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'contact', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'contact', array(
                            'id' => 'contact',
                            'class' => 'form-control',
                            'placeholder' => 'Контактные данные'
                        )); ?>
                        <?php echo $form->error($model,'contact'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'snils', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-5">
                        <?php echo $form->textField($model,'snils', array(
                            'id' => 'snils',
                            'class' => 'form-control',
                            'placeholder' => 'Формат XXX-XXX-XXX-XX'
                        )); ?>
                        <?php echo $form->error($model,'snils'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'invalidGroup', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->dropDownList($model, 'invalidGroup', array('Нет', 'I', 'II', 'III', 'IV'), array(
                            'id' => 'invalidGroup',
                            'class' => 'form-control'
                        )); ?>
                        <?php echo $form->error($model,'invalidGroup'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="add-patient-submit">
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/add'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                    $("#patient-withoutcard-form").trigger("success", [data, textStatus, jqXHR])
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
                    <p>Поздравляем, вы успешно добавили нового пациента и создали для него первую карту. Впоследствии, Вы можете добавлять новые карты при <?php echo CHtml::link('поиске пациента', array('/reception/patient/viewsearch')) ?> или <?php echo CHtml::link('записать', array('#')) ?> добавленного пациента на приём</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>