<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/767e5633/jquery.yiiactiveform.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/searchAddPatient.js" ></script>
<?php if(Yii::app()->user->checkAccess('addPatient')) { ?>
<h4>Регистрация / перерегистрация карты к существующему пациенту (<?php echo $regPoint; ?> год)</h4>
<p class="text-left">
    Заполните поля формы, чтобы зарегистрировать / перерегистрировать карту пациента <span class="text-danger bold">(<?php echo $fio; ?>, полис №<?php echo $policy_number; ?>)</span>
</p>
<div class="row default-padding">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'patient-withcard-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/addcard'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
        <div class="row">
            <div class="col-xs-6">
                <?php echo $form->hiddenField($model,'policy', array(
                    'id' => 'policy',
                    'class' => 'form-control',
                    'value' => $policy_id
                )); ?>
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
                        <span class="help-block">Номер документа может состоять из цифр</span>
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
                    <?php echo $form->labelEx($model,'documentGivedate', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-6 input-group date" id="document-givedate-cont">
                        <?php echo $form->hiddenField($model,'documentGivedate', array(
                            'id' => 'documentGivedate',
                            'class' => 'form-control',
                            'placeholder' => 'Формат гггг-мм-дд'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="date-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-day-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon month-button up-month-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon year-button up-year-button" ></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="day" placeholder="ДД" class="form-control day">
                                <input type="text" name="month" placeholder="ММ" class="form-control month">
                                <input type="text" name="year" placeholder="ГГГГ" class="form-control year">
                            </div>
                            <div class="date-ctrl-down-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-day-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon month-button down-month-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon year-button down-year-button" ></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
            </div>
            <div class="col-xs-6">
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
                    <?php echo $form->labelEx($model,'profession', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->textField($model,'profession', array(
                            'id' => 'profession',
                            'class' => 'form-control',
                            'placeholder' => 'Профессия'
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
                    <div class="col-xs-6">
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
                <div class="form-group">
                    <?php echo $form->labelEx($model,'privilege', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9">
                        <?php echo $form->dropDownList($model, 'privilege', $privilegesList, array(
                            'id' => 'privilege',
                            'class' => 'form-control'
                        )); ?>
                        <?php echo $form->error($model,'privilege'); ?>
                    </div>
                </div>
                <div class="form-group <?php echo $foundPriv ? '' : 'no-display'; ?>">
                    <?php echo $form->labelEx($model,'privDocname', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-6">
                        <?php echo $form->textField($model,'privDocname', array(
                            'id' => 'privDocname',
                            'class' => 'form-control',
                            'placeholder' => 'Название документа'
                        )); ?>
                    </div>
                </div>
                <div class="form-group <?php echo $foundPriv ? '' : 'no-display'; ?>">
                    <?php echo $form->labelEx($model,'privDocserie', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-6">
                        <?php echo $form->textField($model,'privDocserie', array(
                            'id' => 'privDocserie',
                            'class' => 'form-control',
                            'placeholder' => 'Серия'
                        )); ?>
                        <span class="help-block">Номер документа может состоять из цифр</span>
                    </div>
                </div>
                <div class="form-group <?php echo $foundPriv ? '' : 'no-display'; ?>">
                    <?php echo $form->labelEx($model,'privDocnumber', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-8">
                        <?php echo $form->textField($model,'privDocnumber', array(
                            'id' => 'privDocnumber',
                            'class' => 'form-control',
                            'placeholder' => 'Номер документа'
                        )); ?>
                    </div>
                </div>
                <div class="form-group <?php echo $foundPriv ? '' : 'no-display'; ?>">
                    <?php echo $form->labelEx($model,'privDocGivedate', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-6 input-group date" id="priv-document-givedate-cont">
                        <?php echo $form->hiddenField($model,'privDocGivedate', array(
                            'id' => 'privDocGivedate',
                            'class' => 'form-control',
                            'placeholder' => 'Формат гггг-мм-дд'
                        )); ?>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        <div class="subcontrol">
                            <div class="date-ctrl-up-buttons">
                                <div class="btn-group">
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-day-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon month-button up-month-button"></button>
                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon year-button up-year-button" ></button>
                                </div>
                            </div>
                            <div class="form-inline subfields">
                                <input type="text" name="day" placeholder="ДД" class="form-control day">
                                <input type="text" name="month" placeholder="ММ" class="form-control month">
                                <input type="text" name="year" placeholder="ГГГГ" class="form-control year