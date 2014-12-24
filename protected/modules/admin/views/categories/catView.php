<?php $this->widget('application.modules.admin.components.widgets.DoctorCardTabMenu') ?>

<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/categories.js"></script>
<table id="categories"></table>
<div id="categoriesPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addCategorie">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editCategorie">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default" id="deleteCategorie">Удалить запись</button>
</div>

<?php this->widget("application.modals.admin.templates.AddCategory"); ?>
<?php this->widget("application.modals.admin.templates.AddCategoryError"); ?>
<?php this->widget("application.modals.admin.templates.EditCategory"); ?>