<?php $this->widget('application.modules.admin.components.widgets.DoctorCardTabMenu') ?>

<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/guides.js"></script>
<table id="guides"></table>
<div id="guidesPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addGuide">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editGuide">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default" id="deleteGuide">Удалить запись</button>
</div>


<?php $this->widget("application.modals.admin.templates.AddGuide"); ?>
<?php $this->widget("application.modals.admin.templates.AddGuideError"); ?>
<?php $this->widget("application.modals.admin.templates.EditGuide"); ?>
