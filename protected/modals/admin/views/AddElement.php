<? $form = $this->beginWidget('CActiveForm', array(
    'focus' => array($model,'name'),
    'id' => 'element-add-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/elements/add'),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
)); ?>

<div class="modal-body">
<div class="row">
<div class="col-xs-12">
<div class="form-group">
    <?php echo $form->labelEx($model,'type', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->dropDownList($model, 'type', $typesList, array(
            'id' => 'type',
            'class' => 'form-control'
        )); ?>
        <?php echo $form->error($model,'type'); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'categorieId', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->dropDownList($model, 'categorieId', $categoriesList, array(
            'id' => 'categorieId',
            'class' => 'form-control'
        )); ?>
        <?php echo $form->error($model,'categorieId'); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'label', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->textField($model,'label', array(
            'id' => 'label',
            'class' => 'form-control',
            'placeholder' => 'Метка'
        )); ?>
        <?php echo $form->error($model,'label'); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'labelAfter', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->textField($model,'labelAfter', array(
            'id' => 'labelAfter',
            'class' => 'form-control',
            'placeholder' => 'Метка после элемента'
        )); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'labelDisplay', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->textField($model,'labelDisplay', array(
            'id' => 'labelDisplay',
            'class' => 'form-control',
            'placeholder' => 'Метка для отображения у администратора'
        )); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'size', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->textField($model,'size', array(
            'id' => 'size',
            'class' => 'form-control',
            'placeholder' => 'Размер поля'
        )); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'isWrapped', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->dropDownList($model, 'isWrapped', array('Нет', 'Да'), array(
            'id' => 'isWrapped',
            'class' => 'form-control'
        )); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'position', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->textField($model,'position', array(
            'id' => 'position',
            'class' => 'form-control',
            'placeholder' => 'Позиция в категории'
        )); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'guideId', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->dropDownList($model, 'guideId', $guidesList, array(
            'id' => 'guideId',
            'class' => 'form-control',
            'disabled' => true,
            'options' => array('selected' => -1)
        )); ?>
        <?php echo $form->error($model,'guideId'); ?>
    </div>
</div>
<div class="form-group no-display">
    <?php  echo $form->labelEx($model,'defaultValue', array(
        'class' => 'col-xs-3 control-label'
    ));  ?>
    <div class="col-xs-9">
        <?php  echo $form->dropDownList($model, 'defaultValue', $guideValuesList, array(
            'id' => 'defaultValue',
            'class' => 'form-control',
            'disabled' => true,
            'options' => array('selected' => -1)
        ));  ?>
    </div>
</div>
<div class="form-group">
    <?php  echo $form->labelEx($model,'defaultValueText', array(
        'class' => 'col-xs-3 control-label'
    ));  ?>
    <div class="col-xs-9">
        <?php  echo $form->textField($model, 'defaultValueText', array(
            'id' => 'defaultValueText',
            'class' => 'form-control',
        ));  ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'isRequired', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->dropDownList($model, 'isRequired', array('Нет', 'Да'), array(
            'id' => 'isRequired',
            'class' => 'form-control'
        )); ?>
        <?php echo $form->error($model,'isRequired'); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'allowAdd', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->dropDownList($model, 'allowAdd', array('Нет', 'Да'), array(
            'id' => 'allowAdd',
            'class' => 'form-control',
            'disabled' => true,
        )); ?>
        <?php echo $form->error($model,'allowAdd'); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'showDynamic', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->dropDownList($model, 'showDynamic', array('Нет', 'Да'), array(
            'id' => 'showDynamic',
            'class' => 'form-control'
        )); ?>
        <?php echo $form->error($model,'showDynamic'); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'hideLabelBefore', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->dropDownList($model, 'hideLabelBefore', array('Нет', 'Да'), array(
            'id' => 'hideLabelBefore',
            'class' => 'form-control'
        )); ?>
        <?php echo $form->error($model,'hideLabelBefore'); ?>
    </div>
</div>
<div class="table-config-container no-display">
    <div class="form-group">
        <?php echo $form->labelEx($model,'numCols', array(
            'class' => 'col-xs-3 control-label'
        )); ?>
        <div class="col-xs-9">
            <?php echo $form->textField($model,'numCols', array(
                'id' => 'numCols',
                'class' => 'form-control',
                'placeholder' => 'Количество столбцов'
            )); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo $form->labelEx($model,'numRows', array(
            'class' => 'col-xs-3 control-label'
        )); ?>
        <div class="col-xs-9">
            <?php echo $form->textField($model,'numRows', array(
                'id' => 'numRows',
                'class' => 'form-control',
                'placeholder' => 'Количество строк'
            )); ?>
        </div>
        <?php echo $form->hiddenField($model,'config', array(
            'id' => 'config'
        )); ?>
    </div>
    <table class="table-config-headers col-xs-11">
        <thead>
        <tr>
            <td>
                <div class="form-group">
                    <label for="" class="col-xs-9 control-label headersLabel">Нужны заголовки строк</label>
                    <input type="checkbox" class="rowsHeaders" />
                </div>
            </td>
            <td>
                <div class="form-group">
                    <label class="col-xs-9 control-label headersLabel">Нужны заголовки столбцов</label>
                    <input type="checkbox" class="colsHeaders" />
                </div>
            </td>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<!-- Таблица для отображения значений по умолчанию -->
<div class="form-group no-display defaultValuesTable">
    <label>Значения по умолчанию</label>
    <br>
    <div style="overflow-x: auto">
        <table class="controltable" style="width: auto">
            <thead>
            </thead>
            <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="form-group no-display">
    <?php echo $form->labelEx($model,'numberFieldMaxValue', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->numberField($model,'numberFieldMaxValue', array(
            'id' => 'numberFieldMaxValue',
            'class' => 'form-control',
            'placeholder' => 'Максимальное значение'
        )); ?>
    </div>
</div>
<div class="form-group">
    <?php  echo $form->labelEx($model,'dateFieldMaxValue', array(
        'class' => 'col-xs-3 control-label'
    ));  ?>
    <div class="col-xs-6 input-group date" id="date-max-field-cont">
        <?php  echo $form->hiddenField($model,'dateFieldMaxValue', array(
            'id' => 'dateFieldMaxValue',
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
<div class="form-group no-display">
    <?php echo $form->labelEx($model,'numberFieldMinValue', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->numberField($model,'numberFieldMinValue', array(
            'id' => 'numberFieldMinValue',
            'class' => 'form-control',
            'placeholder' => 'Минимальное значение'
        )); ?>
    </div>
</div>
<div class="form-group">
    <?php  echo $form->labelEx($model,'dateFieldMinValue', array(
        'class' => 'col-xs-3 control-label'
    ));  ?>

    <div class="col-xs-6 input-group date" id="date-min-field-cont">
        <?php  echo $form->hiddenField($model,'dateFieldMinValue', array(
            'id' => 'dateFieldMinValue',
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
<div class="form-group no-display">
    <?php echo $form->labelEx($model,'numberStep', array(
        'class' => 'col-xs-3 control-label'
    )); ?>
    <div class="col-xs-9">
        <?php echo $form->numberField($model,'numberStep', array(
            'id' => 'numberStep',
            'class' => 'form-control',
            'placeholder' => 'Шаг'
        )); ?>
    </div>
</div>
</div>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    <?php echo CHtml::ajaxSubmitButton(
        'Добавить',
        CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/elements/add'),
        array(
            'success' => 'function(data, textStatus, jqXHR) {
                                $("#element-add-form").trigger("success", [data, textStatus, jqXHR])
                            }'
        ),
        array(
            'class' => 'btn btn-primary'
        )
    ); ?>
</div>

<? $this->endWidget(); ?>