<?php $this->widget('application.modules.admin.components.widgets.DoctorCardTabMenu');  ?>

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

<?php $this->widget('application.modals.admin.templates.AddTemplate') ?>
<?php $this->widget('application.modals.admin.templates.AddTemplateError') ?>
<?php $this->widget('application.modals.admin.templates.EditTemplate') ?>
<?php $this->widget('application.modals.admin.templates.ShowTemplate') ?>
<?php $this->widget('application.modals.admin.templates.DesignTemplate') ?>
<?php $this->widget("application.modals.admin.templates.AddCategory"); ?>
<?php $this->widget("application.modals.admin.templates.AddCategoryError"); ?>
<?php $this->widget("application.modals.admin.templates.EditCategory"); ?>
<?php $this->widget("application.modals.admin.templates.AddElement"); ?>
<?php $this->widget("application.modals.admin.templates.AddElementError"); ?>
<?php $this->widget("application.modals.admin.templates.EditElement"); ?>
<?php $this->widget("application.modals.admin.templates.EditDependences"); ?>
<?php $this->widget("application.modals.admin.templates.RemoveCategory"); ?>
<?php $this->widget("application.modals.admin.templates.FindCategory"); ?>
<?php $this->widget("application.modals.admin.templates.IssetMedworkerNotice"); ?>
