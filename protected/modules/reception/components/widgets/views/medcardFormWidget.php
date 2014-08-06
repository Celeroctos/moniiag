<?php
   // var_dump($model);
//exit();
?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<p class="bold text-danger no-display noTasuConnection">ВНИМАНИЕ! ТАСУ недоступна: внимательно проверьте реквизиты полиса перед сохранением данных.</p>
<div class="form-group">
    <?php echo $form->labelEx($model,'doctype', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-5">
        <?php echo $form->dropDownList($model, 'doctype', $docTypesList, array(
            'id' => 'doctype',
            'class' => 'form-control'
        )); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'serie', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->textField($model,'serie', array(
            'id' => 'serie',
            'class' => 'form-control',
            'placeholder' => 'Серия'
        )); ?>
        <?php echo $form->textField($model,'docnumber', array(
            'id' => 'docnumber',
            'class' => 'form-control',
            'placeholder' => 'Номер',
            'data-toggle' => 'tooltip',
            'data-placement' => 'right',
            'title' => 'Номер документа может состоять из цифр'
        )); ?>
    </div>
</div>
<!--Поле "Кем выдан" - не используется-->
<!--    <div class="form-group"> -->
        <?php  /*echo $form->labelEx($model,'whoGived', array(
            'class' => 'col-xs-3 control-label'
        )); */?>

<!--            <div class="col-xs-8">-->
            <?php /* echo $form->textField($model,'whoGived', array(
                'id' => 'whoGived',
                'class' => 'form-control',
                'placeholder' => 'Кем выдан'
            )); */ ?>
<!--            </div>
    </div>-->

<!--Поле "Дата выдачи" - не нужно-->
<!--<div class="form-group">-->
    <?php /* echo $form->labelEx($model,'documentGivedate', array(
        'class' => 'col-xs-3 control-label'
    )); */ ?>
<!--    <div class="col-xs-6 input-group date" id="document-givedate-cont">
        <?php /* echo $form->hiddenField($model,'documentGivedate', array(
            'id' => 'documentGivedate',
            'class' => 'form-control',
            'placeholder' => 'Формат гггг-мм-дд'
        )); */ ?>
<!--       <span class="input-group-addon">
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
</div>-->

<div class="form-group">
    <?php echo $form->labelEx($model,'addressReg', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->textField($model,'addressReg', array(
            'id' => 'addressReg',
            'class' => 'form-control blockEdit',
            'placeholder' => 'Адрес регистрации'
        )); ?>
        <?php echo $form->hiddenField($model,'addressRegHidden', array(
            'id' => 'addressRegHidden',
            'class' => 'form-control',
        )); ?>
        <?php if(isset($showEditIcon) && $showEditIcon == 1) { ?>
        <a href="#" class="editAddress" title="Редактировать адрес регистрации">
            <span class="glyphicon glyphicon-pencil"></span>
        </a>
        <?php } ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'address', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->textField($model,'address', array(
            'id' => 'address',
            'class' => 'form-control blockEdit',
            'placeholder' => 'Адрес проживания'
        )); ?>
        <?php echo $form->hiddenField($model,'addressHidden', array(
            'id' => 'addressHidden',
            'class' => 'form-control',
            'placeholder' => 'Адрес проживания'
        )); ?>
        <?php if(isset($showEditIcon) && $showEditIcon == 1) { ?>
        <a href="#" class="editAddress" title="Редактировать адрес проживания">
            <span class="glyphicon glyphicon-pencil"></span>
        </a>
        <?php } ?>
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
            'data-toggle' => 'tooltip',
            'data-placement' => 'right',
            'title' => 'Номер телефона пациента, по которому с ним можно связаться'
        )); ?>
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
            'placeholder' => 'Формат XXX-XXX-XXX-XX',
            'data-toggle' => 'tooltip',
            'data-placement' => 'right',
            'title' => 'Страховой номер индивидуального лицевого счета гражданина в формате XXX-XXX-XXX-XX, где X - цифра'
        )); ?>
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
    </div>
</div>
<div class="form-group no-display">
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
<div class="form-group no-display">
    <?php echo $form->labelEx($model,'privDocserie', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-6">
        <?php echo $form->textField($model,'privDocserie', array(
            'id' => 'privDocserie',
            'class' => 'form-control',
            'placeholder' => 'Серия',
            'title' => 'Номер документа может состоять из цифр'
        )); ?>
    </div>
</div>
<div class="form-group no-display">
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
<div class="form-group no-display">
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