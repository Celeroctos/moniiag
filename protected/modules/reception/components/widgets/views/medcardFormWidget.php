<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<div class="form-group">
    <?php echo $form->labelEx($model,'doctype', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-5">
        <?php echo $form->dropDownList($model, 'doctype', array(1 => 'Паспорт'), array(
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
            'placeholder' => 'Контактные данные',
            'data-toggle' => 'tooltip',
            'data-placement' => 'right',
            'title' => 'Здесь могут быть любые данные, позволяющие связаться с пациентом (например, телефон, адрес эл. почты)'
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
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'address-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
));
?>
<div class="modal fade error-popup" id="editAddressPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактирование адреса</h4>
            </div>
            <div class="modal-body">
                <div class="form-group chooser" id="regionChooser">
                    <label for="region" class="col-xs-4 control-label">Регион (Enter - добавить)</label>
                    <div class="col-xs-7">
                        <input type="text" class="form-control" id="region" placeholder="Регион">
                        <ul class="variants no-display">
                        </ul>
                        <div class="choosed">
                        </div>
                    </div>
                </div>
                <div class="form-group chooser" id="districtChooser">
                    <label for="district" class="col-xs-4 control-label">Район (Enter - добавить)</label>
                    <div class="col-xs-7">
                        <input type="text" class="form-control" id="district" placeholder="Район" >
                        <ul class="variants no-display">
                        </ul>
                        <div class="choosed">
                        </div>
                    </div>
                </div>
                <div class="form-group chooser" id="settlementChooser">
                    <label for="settlement" class="col-xs-4 control-label">Населённый пункт (Enter - добавить)</label>
                    <div class="col-xs-7">
                        <input type="text" class="form-control" id="settlement" placeholder="Населённый пункт">
                        <ul class="variants no-display">
                        </ul>
                        <div class="choosed">
                        </div>
                    </div>
                </div>
                <div class="form-group chooser" id="streetChooser">
                    <label for="street" class="col-xs-4 control-label">Улица (Enter - добавить)</label>
                    <div class="col-xs-7">
                        <input type="text" class="form-control" id="street" placeholder="Улица">
                        <ul class="variants no-display">
                        </ul>
                        <div class="choosed">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="house" class="col-xs-4 control-label">Дом</label>
                    <div class="col-xs-7">
                        <input type="text" class="form-control" id="house" placeholder="Дом">
                    </div>
                </div>
                <div class="form-group">
                    <label for="building" class="col-xs-4 control-label">Корпус</label>
                    <div class="col-xs-7">
                        <input type="text" class="form-control" id="building" placeholder="Корпус">
                    </div>
                </div>
                <div class="form-group">
                    <label for="flat" class="col-xs-4 control-label">Квартира</label>
                    <div class="col-xs-7">
                        <input type="text" class="form-control" id="flat" placeholder="Квартира">
                    </div>
                </div>
                <div class="form-group">
                    <label for="postindex" class="col-xs-4 control-label">Почтовый индекс</label>
                    <div class="col-xs-7">
                        <input type="text" class="form-control" id="postindex" placeholder="Почтовый индекс">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-success editSubmit">Сохранить адрес</button>
            </div>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>