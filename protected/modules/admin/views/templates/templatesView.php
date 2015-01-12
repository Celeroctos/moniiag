<?php $this->widget('application.modules.admin.components.widgets.DoctorCardTabMenu') ?>

<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/jquery-ui-1.11.2.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/nestable.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/template-engine.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/templates.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/elements.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/categories.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/common.js"></script>
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
<? $this->widget("application.modals.admin.templates.AddCategory"); ?>
<? $this->widget("application.modals.admin.templates.AddCategoryError"); ?>
<? $this->widget("application.modals.admin.templates.EditCategory"); ?>
<? $this->widget("application.modals.admin.templates.AddElement"); ?>
<? $this->widget("application.modals.admin.templates.AddElementError"); ?>
<? $this->widget("application.modals.admin.templates.EditElement"); ?>
<? $this->widget("application.modals.admin.templates.EditDependences"); ?>
<? $this->widget("application.modals.admin.templates.RemoveCategory"); ?>
<? $this->widget("application.modals.admin.templates.FindCategory"); ?>
<? $this->widget("application.modals.admin.templates.IssetMedworkerNotice"); ?>