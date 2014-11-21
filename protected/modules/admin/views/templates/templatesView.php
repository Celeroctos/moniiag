<?php $this->widget('application.modules.admin.components.widgets.DoctorCardTabMenu') ?>

<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/jquery-ui-1.11.2.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/nestable.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/template-engine.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/templates.js"></script>
<table id="templates"></table>
<div id="templatesPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addTemplate">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editTemplate">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default" id="designTemplate">Дизайнер</button>
    <button type="button" class="btn btn-default" id="deleteTemplate">Удалить запись</button>
    <button type="button" class="btn btn-success" id="showTemplate">Просмотр шаблона</button>
</div>

<? $this->widget('application.modals.admin.templates.AddTemplate') ?>
<? $this->widget('application.modals.admin.templates.AddTemplateError') ?>
<? $this->widget('application.modals.admin.templates.EditTemplate') ?>
<? $this->widget('application.modals.admin.templates.ShowTemplate') ?>
<? $this->widget('application.modals.admin.templates.DesignTemplate') ?>