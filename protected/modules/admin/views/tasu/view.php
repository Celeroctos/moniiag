<link href="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/fileuploader/style.css" rel="stylesheet" type="text/css" media="screen"  />
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/fileuploader/script.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/fileuploader.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/tablechooser.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/tasu.js" ></script>
<h4>Инструменты интеграции с ТАСУ</h4>
<p>Раздел предлагает инструменты управления интеграцией с ТАСУ (Типовой Автоматизированной Системой Управления) ОМС.</p>
<?php $this->widget('application.modules.admin.components.widgets.UploadTasuTypesTabMenu', array(
    ));
?>
<h4>Загрузка данных в базу данных из CSV</h4>
<div class="row">
    <form class="form-horizontal col-xs-12" role="form" id="tasu-upload-form">
        <div class="fileinput-group" id="tasuIn">
            <div class="fileinput-wrap">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <span class="btn btn-default btn-file">
                        <span class="fileinput-new">Выберите файл...</span>
                        <span class="fileinput-exists">Перевыбрать</span>
                        <input type="file" name="...">
                    </span>
                    <span class="fileinput-filename"></span>
                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                    <div class="progress progress-striped active no-display">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                            <span class="sr-only">0% завершено</span>
                        </div>
                        <span class="description">0% завершено</span>
                        <span class="text-success ok no-display">
                            <span class="glyphicon glyphicon-chevron-down"></span>
                        </span>
                    </div>
                    <button class="btn btn-default btn-sm plus no-display" type="button">
                        <span class="glyphicon glyphicon-plus"></span>
                    </button>
                </div>
            </div>
            <div>
                <input type="button" value="Загрузить" class="btn btn-success submit" >
            </div>
            <div class="no-display">
                <input type="button" value="OK" class="btn btn-success successUpload">
            </div>
        </div>
    </form>
</div>
<h4 class="fileName"></h4>
<p>Кликните на имени файла, чтобы выбрать его для импорта</p>
<div id='filesList' class="row borderedBox">
    <table class="table table-condensed table-hover">
        <thead>
        <tr class="header">
            <td>
                Имя файла
            </td>
            <td>
                Тип
            </td>
        </tr>
        </thead>
        <tbody>
            <?php foreach($filesList as $file) { ?>
                <tr>
                    <td>
                        <a id="#i<?php echo $file['id']; ?>" href="#i<?php echo $file['id']; ?>" class='fileName'><?php echo $file['realName']; ?></a>
                    </td>
                    <td>
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/<?php echo $file['icon']; ?>" width="24" height="24" title="CSV" alt="CSV" />
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<div class="row no-display" id='chooseTable'>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'tasu-uploaded-file',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/reception/patient/addcard'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
        <h5><strong>Выберите таблицу для импорта</strong></h5>
        <div class="form-group">
            <div class="col-xs-9">
                <?php echo $form->dropDownList($model, 'table', $tables, array(
                    'id' => 'tableList',
                    'class' => 'form-control',
                    'disabled' => 'true'
                )); ?>
            </div>
        </div>
        <div class="row borderedBox fieldsBox no-display">
            <h5><strong>Поля для импорта</strong></h5>
            <p>Выберите импортируемые в таблицу поля из файла и проставьте им соответствующие поля из таблицы базы данных</p>
            <div class="clear">
                <div class="col-xs-7">
                    <table id="tableFields">
                        <thead class="bold">
                            <td></td>
                            <td>Поле базы</td>
                            <td>Поле файла</td>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-default btn-sm plus">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </button>
                                </td>
                                <td>
                                    <?php echo $form->dropDownList($modelTasuImportField, 'dbField', array(), array(
                                        'class' => 'form-control dbField'
                                    )); ?>
                                </td>
                                <td>
                                    <?php echo $form->dropDownList($modelTasuImportField, 'tasuField', array(), array(
                                        'class' => 'form-control tasuField'
                                    )); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-xs-5">
                    <div id='fieldTemplatesList' class="row borderedBox">
                        <h5><strong>Шаблоны</strong></h5>
                        <p>Здесь вы можете выбрать один из сохранённых шаблонов полей</p>
                        <table class="table table-condensed table-hover">
                            <thead>
                            <tr class="header">
                                <td></td>
                                <td>
                                    Имя шаблона
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($fieldsTemplatesList as $template) { ?>
                                <tr>
                                    <td>
                                        <a id="#d<?php echo $template['id']; ?>" href="#d<?php echo $template['id']; ?>" class='text-danger'>
                                            <span class="glyphicon glyphicon-remove" title="Удалить"></span>
                                        </a>
                                    </td>
                                    <td>
                                        <a id="#i<?php echo $template['id']; ?>" href="#i<?php echo $template['id']; ?>" class='fieldsTemplatesName'><?php echo $template['name']; ?></a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-group clear">
                <input type="button" class="btn btn-success saveAsTemplate" value="Сохранить как шаблон полей">
            </div>
        </div>
        <div class="row borderedBox keyBox no-display">
            <h5><strong>Ключ</strong></h5>
            <p>Ключ - это набор полей, по которым будет проводиться сравнение с базой. В том случае, если все поля из ключа в файле совпадают с соответствующими полями из базы, набор импортируемых данных будет считаться уже существующим и, соответственно, игнорироваться.</p>
            <div class="clear">
                <div class="col-xs-7">
                    <table id="tableKey">
                        <thead class="bold">
                            <td></td>
                            <td>Поле базы</td>
                        </thead>
                        <tbody>
                            <td>
                                <button type="button" class="btn btn-default btn-sm plus">
                                    <span class="glyphicon glyphicon-plus" title="Удалить"></span>
                                </button>
                            </td>
                            <td>
                                <?php echo $form->dropDownList($modelTasuImportField, 'dbField', array(), array(
                                    'class' => 'form-control dbField'
                                )); ?>
                            </td>
                        </tbody>
                    </table>
                </div>
                <div class="col-xs-5">
                    <div id='keyTemplatesList' class="row borderedBox">
                        <h5><strong>Шаблоны</strong></h5>
                        <p>Здесь вы можете выбрать один из сохранённых шаблонов ключа</p>
                        <table class="table table-condensed table-hover">
                            <thead>
                            <tr class="header">
                                <td></td>
                                <td>
                                    Имя шаблона
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($keysTemplatesList as $template) { ?>
                                <tr>
                                    <td>
                                        <a id="#d<?php echo $template['id']; ?>" href="#d<?php echo $template['id']; ?>" class='text-danger'>
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </a>
                                    </td>
                                    <td>
                                        <a id="#i<?php echo $template['id']; ?>" href="#i<?php echo $template['id']; ?>" class='keysTemplatesName'><?php echo $template['name']; ?></a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-group clear">
                <input type="button" class="btn btn-success saveAsKey" value="Сохранить как шаблон ключа">
            </div>
        </div>
        <div class="row submitImport">
            <input type="button" value="Импортировать" class="btn btn-success disabled">
        </div>
        <div class="row borderedBox default-margin-top progressBox no-display">
            <h5><strong>Прогресс импорта</strong></h5>
            <div class="progress progress-striped active">
                <div class="progress-bar progress-bar-warning" id="importProgressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                    <span class="sr-only"></span>
                </div>
            </div>
            <p class="text-warning">Всего (строк): <span class="numStringsAll">0</span></p>
            <p class="text-primary">Обработано (строк): <span class="numStrings">0</span></p>
            <p class="text-success">Добавлено (строк): <span class="numStringsAdded">0</span></p>
            <p class="text-danger">Отклонено (строк): <span class="numStringsDiscarded">0</span></p>
            <p class="text-danger"><strong>Ошибок (строк): <span class="numStringsError">0</span></strong></p>
            <div class="form-group clear">
                <input type="button" class="btn btn-success successImport no-display" value="Закончить импорт">
                <input type="button" class="btn btn-danger pauseImport" value="Пауза">
                <input type="button" class="btn btn-danger continueImport disabled" value="Продолжить">
            </div>
        </div>
    <?php $this->endWidget(); ?>
</div>
<div class="modal fade" id="addFieldTemplate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить шаблон файла</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($modelAddFieldTemplate,'name'),
                'id' => 'field-template-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
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
                            <?php echo $form->hiddenField($modelAddFieldTemplate,'table', array(
                                'id' => 'table',
                                'class' => 'form-control'
                            )); ?>
                            <?php echo $form->hiddenField($modelAddFieldTemplate,'template', array(
                                'id' => 'template',
                                'class' => 'form-control'
                            )); ?>
                            <?php echo $form->labelEx($modelAddFieldTemplate,'name', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($modelAddFieldTemplate,'name', array(
                                    'id' => 'name',
                                    'class' => 'form-control',
                                    'placeholder' => 'Название шаблона'
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
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/tasu/addfieldtemplate'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#field-template-add-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade" id="addKeyTemplate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить шаблон ключа</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($modelAddFieldTemplate,'name'),
                'id' => 'key-template-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
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
                            <?php echo $form->hiddenField($modelAddKeyTemplate,'table', array(
                                'id' => 'table',
                                'class' => 'form-control'
                            )); ?>
                            <?php echo $form->hiddenField($modelAddKeyTemplate,'template', array(
                                'id' => 'template',
                                'class' => 'form-control'
                            )); ?>
                            <?php echo $form->labelEx($modelAddKeyTemplate,'name', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($modelAddKeyTemplate,'name', array(
                                    'id' => 'name',
                                    'class' => 'form-control',
                                    'placeholder' => 'Название шаблона'
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
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/admin/tasu/addkeytemplate'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#key-template-add-form").trigger("success", [data, textStatus, jqXHR])
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
<div class="modal fade error-popup" id="errorPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Ошибка!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Произошла ошибка при загрузке файла.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    globalVariables.uploadInfoFieldName = '<?php echo ini_get("session.upload_progress.name"); ?>';
</script>
<iframe id="fileIframe" name='fileIframe'  class='no-display' src="<?php echo Yii::app()->request->baseUrl; ?>/admin/tasu/view?iframe"></iframe>