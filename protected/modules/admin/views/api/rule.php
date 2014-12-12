<?php $this->widget('application.modules.admin.components.widgets.ApiTabMenu') ?>

<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/api.js"></script>
<table id="apiRuleGrid"></table>
<div id="apiRuleGridPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addApiRule">Добавить правило</button>
    <button type="button" class="btn btn-danger" id="deleteApiRule">Удалить правило</button>
</div>

<? $this->widget("application.modals.admin.templates.AddApiRule"); ?>
<?// $this->widget("application.modals.admin.templates.EditApiRule"); ?>
<? $this->widget("application.modals.admin.templates.AddCategoryError"); ?>