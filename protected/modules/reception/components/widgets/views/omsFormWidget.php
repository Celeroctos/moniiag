<?php
  //  var_dump($model);
  //  exit();
?>
<div class="form-group no-display" id="insuranceHidden">
    <?php echo $form->hiddenField($model,'insurance', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
</div>
<div class="form-group no-display" id="policyRegionHidden">
    <?php echo $form->hiddenField($model,'region', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'policy', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-5">
        <?php echo $form->textField($model,'policy', array(
            'id' => 'policy',
            'class' => 'form-control',
            'placeholder' => 'ОМС',
            'autofocus'=> '1',
            'data-toggle' => 'tooltip',
            'data-placement' => 'right',
            'title' => 'Номер полиса ОМС может состоять из цифр и пробелов'
        )); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'status', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-5">
        <?php echo $form->dropDownList($model, 'status', array('Активен', 'Погашен'), array(
            'id' => 'status',
            'class' => 'form-control'
        )); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'omsType', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-5">
        <?php echo $form->dropDownList($model, 'omsType', array('Постоянный', 'Временный'), array(
            'id' => 'omsType',
            'class' => 'form-control'
        )); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'policyGivedate', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-6 input-group date" id="policy-givedate-cont">
        <?php echo $form->hiddenField($model,'policyGivedate', array(
            'id' => 'policyGivedate',
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
<!-- чюзер с регионом для полиса -->
<div class="form-group chooser" id="regionPolicyChooser">
    <label for="doctor" class="col-xs-3 control-label">Регион: </label>
    <div class="col-xs-9">
        <input type="text" class="form-control" id="doctor"
               placeholder="Начинайте вводить...">
        <ul class="variants no-display">
        </ul>
        <div class="choosed">
        </div>
    </div>
</div>
<!-- Здесь будет большой, красивы чюзер с выбором страховой компании -->
<div class="form-group chooser" id="insuranceChooser">
    <label for="doctor" class="col-xs-3 control-label">Страховая компания: </label>

    <div class="col-xs-9">
        <input type="text" class="form-control" id="doctor"
               placeholder="Начинайте вводить...">
        <ul class="variants no-display">
        </ul>
        <div class="choosed">
        </div>
    </div>
</div>
<!-- -->

<div class="form-group no-display policy-enddate">
    <?php echo $form->labelEx($model,'policyEnddate', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-6 input-group date" id="policy-enddate-cont">
        <?php echo $form->hiddenField($model,'policyEnddate', array(
            'id' => 'policyEnddate',
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
    <?php echo $form->labelEx($model,'lastName', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->textField($model,'lastName', array(
            'id' => 'lastName',
            'class' => 'form-control',
            'placeholder' => 'Фамилия',
            'data-toggle' => 'tooltip',
            'data-placement' => 'right',
            'title' => 'Фамилия может состоять из кириллицы и дефисов (двойные фамилии)'
        )); ?>
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
            'placeholder' => 'Имя',
            'data-toggle' => 'tooltip',
            'data-placement' => 'right',
            'title' => 'Имя может состоять из кириллицы и дефисов'
        )); ?>
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
            'placeholder' => 'Отчество',
            'data-toggle' => 'tooltip',
            'data-placement' => 'right',
            'title' => 'Отчество может состоять из кириллицы и дефисов. Это необязательное поле.'
        )); ?>
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
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'birthday', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-6 input-group date" id="birthday-cont">
        <?php echo $form->hiddenField($model,'birthday', array(
            'id' => 'birthday',
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