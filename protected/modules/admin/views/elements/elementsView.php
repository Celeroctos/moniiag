<?php $this->widget('application.modules.admin.components.widgets.DoctorCardTabMenu') ?>

<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/elements.js"></script>
<table id="elements"></table>
<div id="elementsPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addElement">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editElement">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default disabled" id="editElementDependences">Редактировать зависимости элемента</button>
    <button type="button" class="btn btn-default" id="deleteElement">Удалить запись</button>
</div>

<? $this->widget("application.modals.admin.templates.AddElement"); ?>
<? $this->widget("application.modals.admin.templates.AddElementError"); ?>
<? $this->widget("application.modals.admin.templates.EditElement"); ?>
<? $this->widget("application.modals.admin.templates.EditDependences"); ?>