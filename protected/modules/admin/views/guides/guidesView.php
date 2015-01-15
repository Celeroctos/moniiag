<?php $this->widget('application.modules.admin.components.widgets.DoctorCardTabMenu') ?>

<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/guides.js"></script>
<table id="guides"></table>
<div id="guidesPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addGuide">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editGuide">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default" id="deleteGuide">Удалить запись</button>
</div>

<? $this->widget("application.modals.admin.AddGuide"); ?>
<? $this->widget("application.modals.admin.AddGuideError"); ?>
<? $this->widget("application.modals.admin.EditGuide"); ?>