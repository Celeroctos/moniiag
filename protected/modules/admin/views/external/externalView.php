<?php $this->widget('application.modules.admin.components.widgets.ExternalTabMenu') ?>

<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/external.js"></script>
<table id="keys"></table>
<div id="keysPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addExternal">Добавить ключ</button>
    <button type="button" class="btn btn-default" id="editExternal">Редактировать описание ключа</button>
    <button type="button" class="btn btn-danger" id="deleteExternal">Отозвать ключ</button>
</div>

<? $this->widget("application.modals.admin.templates.AddExternal"); ?>
<? $this->widget("application.modals.admin.templates.EditExternal"); ?>
<? $this->widget("application.modals.admin.templates.AddCategoryError"); ?>