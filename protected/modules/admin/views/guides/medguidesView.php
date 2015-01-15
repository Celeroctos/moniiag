<?php $this->widget('application.modules.admin.components.widgets.MedguidesTabMenu') ?>

<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/medguides.js"></script>

<?php if($currentGuideId != -1) : ?>

    <script type="text/javascript">
        globalVariables.currentGuideId = <?php echo $currentGuideId; ?>;
    </script>
    <table id="medguides"></table>
    <div id="medguidesPager"></div>
    <div class="btn-group default-margin-top">
        <button type="button" class="btn btn-default" id="addMedGuide">Добавить запись</button>
        <button type="button" class="btn btn-default" id="editMedGuide">Редактировать выбранную запись</button>
        <button type="button" class="btn btn-default" id="deleteMedGuide">Удалить запись</button>
    </div>

    <? $this->widget("application.modals.admin.templates.AddMedGuide"); ?>
    <? $this->widget("application.modals.admin.templates.AddMedGuideError"); ?>
    <? $this->widget("application.modals.admin.templates.EditMedGuide"); ?>

<?php endif; ?>