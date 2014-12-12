<?php $this->widget('application.modules.admin.components.widgets.ApiTabMenu') ?>

<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/api.js"></script>
<table id="apiGrid"></table>
<div id="apiGridPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addApi">Добавить ключ</button>
    <button type="button" class="btn btn-default" id="editApi">Редактировать описание ключа</button>
    <button type="button" class="btn btn-danger" id="deleteApi">Отозвать ключ</button>
</div>

<? $this->widget("application.modals.admin.templates.AddApi"); ?>
<? $this->widget("application.modals.admin.templates.EditApi"); ?>
<? $this->widget("application.modals.admin.templates.AddCategoryError"); ?>