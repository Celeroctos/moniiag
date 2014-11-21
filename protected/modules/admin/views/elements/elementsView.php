<h4>Краткая справка</h4>
<p>Раздел предназначен для редактирования содержания медицинской карты для рабочего места врача. Карта у врача разбита на категории (раскрывающиеся списки), внутри них имеются управляющие элементы, которые могут представлять собой, в том числе, выбор значения из справочника.
    При формировании шаблона карты требуется определить группы, поля карты, справочники и привязать последние к определённым полям. Справочники при необходимости можно дополнять значениями.
</p>

<?php $this->widget('application.components.widgets.DoctorCardTabMenu') ?>

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