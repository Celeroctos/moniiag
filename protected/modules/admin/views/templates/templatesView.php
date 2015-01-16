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


<? $this->widget('application.modals.admin.AddTemplate') ?>
<? $this->widget('application.modals.admin.EditTemplate') ?>
<? $this->widget('application.modals.admin.AddTemplateError') ?>
<? $this->widget('application.modals.admin.ShowTemplate') ?>
<? $this->widget('application.modals.admin.DesignTemplate') ?>
<? $this->widget("application.modals.admin.AddCategory"); ?>
<? $this->widget("application.modals.admin.AddCategoryError"); ?>
<? $this->widget("application.modals.admin.EditCategory"); ?>
<? $this->widget("application.modals.admin.AddElement"); ?>
<? $this->widget("application.modals.admin.AddElementError"); ?>
<? $this->widget("application.modals.admin.EditElement"); ?>
<? $this->widget("application.modals.admin.EditDependences"); ?>
<? $this->widget("application.modals.admin.RemoveCategory"); ?>
<? $this->widget("application.modals.admin.FindCategory"); ?>
<? $this->widget("application.modals.admin.IssetMedworkerNotice"); ?>
