<div class="row">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'employee-filter-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
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
                'class' => 'form-control'
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
            )); ?>
            <?php echo $form->error($modelFilter,'wardCode'); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo CHtml::ajaxSubmitButton(
            'Фильтровать',
            CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/employees/filter'),
            array(
                'success' => 'function(data, textStatus, jqXHR) {
                                    $("#employee-filter-form").trigger("success", [data, textStatus, jqXHR])
                                }'
            ),
            array(
                'class' => 'btn btn-success'
            )
        ); ?>
    </div>
    <?php $this->endWidget(); ?>
</div>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/employees.js"></script>
<script type="text/javascript">
    globalVariables.guideEdit = '<?php echo Yii::app()->user->checkAccess('guideEditEmployee'); ?>';
</script>
<table id="employees"></table>
<div id="employeesPager"></div>
<div class="btn-group default-margin-top">
    <?php if(Yii::app()->user->checkAccess('guideAddEmployee')) { ?>
    <button type="button" class="btn btn-default" id="addEmployee">Добавить запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideEditEmployee')) { ?>
    <button type="button" class="btn btn-default" id="editEmployee">Редактировать выбранную запись</button>
    <?php } ?>
    <?php if(Yii::app()->user->checkAccess('guideDeleteEmployee')) { ?>
    <button type="button" class="btn btn-default" id="deleteEmployee">Удалить запись</button>
    <?php } ?>
</div>
<div class="modal fade" id="addEmployeePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить персонал</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'shortName'),
                'id' => 'employee-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/employees/add'),
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
                            <?php echo $form->labelEx($model,'degreeId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'degreeId', $degreesList, array(
                                    'id' => 'degreeId',
                                    'class' => 'form-control',
                                    'options' => array('-1' => array('selected' => true))
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
                                    'class' => 'form-control',
                                    'options' => array('-1' => array('selected' => true))
                                )); ?>
                                <?php echo $form->error($model,'titulId'); ?>
                            </div>
                        </div>
						<div class="form-group">
                            <?php echo $form->labelEx($model,'categorie', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'categorie', $categoriesList, array(
                                    'id' => 'categorie',
                                    'class' => 'form-control',
                                    'options' => array('0' => array('selected' => true))
                                )); ?>
                            </div>
                        </div>
                        <div class="form-group">
						<?php echo $form->labelEx($model,'dateBegin', array(
											'class' => 'col-xs-3 control-label'
										)); ?>
							<div class="col-xs-9 input-group date" id="dateBegin-cont">
								<?php echo $form->hiddenField($model,'dateBegin', array(
											'id' => 'dateBegin',
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
									<label for="notDateEnd" class="col-xs-11 control-label">
										Не ограничивать сотрудника по времени
									</label>
									<input type="checkbox" id="notDateEnd" name="notDateEnd">
                            </div>
						    <div class="form-group">
									   <?php echo $form->labelEx($model,'dateEnd', array(
								'class' => 'col-xs-3 control-label'
							)); ?>
							<div class="col-xs-9 input-group date" id="dateEnd-cont">
								<?php echo $form->hiddenField($model,'dateEnd', array(
											'id' => 'dateEnd',
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
                            <?php echo $form->labelEx($model,'wardCode', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'wardCode', $wardsListForAdd, array(
                                    'id' => 'wardCode',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'wardCode'); ?>
                            </div>
                        </div>
						<div class="form-group">
                            <?php echo $form->labelEx($model,'greetingType', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'greetingType', array('Любой', 'Первичный', 'Повторный'), array(
                                    'id' => 'greetingType',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'greetingType'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->label($model,'displayInCallcenter', array(
                                'class' => 'col-xs-3 control-label text-left'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'displayInCallcenter', array('Нет', 'Да'), array(
                                    'id' => 'displayInCallcenter',
                                    'class' => 'form-control',
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
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/employees/add'),
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
<div class="modal fade" id="editEmployeePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать персонал</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'shortName'),
                'id' => 'employee-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/employees/edit'),
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
                            <?php echo $form->labelEx($model,'categorie', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'categorie', $categoriesList, array(
                                    'id' => 'categorie',
                                    'class' => 'form-control',
                                    'options' => array('0' => array('selected' => true))
                                )); ?>
                            </div>
                        </div>
                        <div class="form-group">
                        <?php echo $form->labelEx($model,'dateBegin', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9 input-group date" id="dateBeginEdit-cont">
                        <?php echo $form->hiddenField($model,'dateBegin', array(
                                    'id' => 'dateBegin',
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
                        <label for="notDateEndEdit" class="col-xs-11 control-label">
                            Не ограничивать сотрудника по времени
                        </label>
                        <input type="checkbox" id="notDateEndEdit" name="notDateEnd">
                    </div>
                    <div class="form-group">
                    <?php echo $form->labelEx($model,'dateEnd', array(
                        'class' => 'col-xs-3 control-label'
                    )); ?>
                    <div class="col-xs-9 input-group date" id="dateEndEdit-cont">
                        <?php echo $form->hiddenField($model,'dateEnd', array(
                                    'id' => 'dateEnd',
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
                            <?php echo $form->labelEx($model,'wardCode', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'wardCode', $wardsListForAdd, array(
                                    'id' => 'wardCode',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'wardCode'); ?>
                            </div>
                        </div>
						<div class="form-group">
                            <?php echo $form->labelEx($model,'greetingType', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'greetingType', array('Любой', 'Первичный', 'Повторный'), array(
                                    'id' => 'greetingType',
                                    'class' => 'form-control'
                                )); ?>
                                <?php echo $form->error($model,'greetingType'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->label($model,'displayInCallcenter', array(
                                'class' => 'col-xs-3 control-label text-left'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model, 'displayInCallcenter', array('Нет', 'Да'), array(
                                    'id' => 'displayInCallcenter',
                                    'class' => 'form-control',
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
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/employees/edit'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#employee-edit-form").trigger("success", [data, textStatus, jqXHR])
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
