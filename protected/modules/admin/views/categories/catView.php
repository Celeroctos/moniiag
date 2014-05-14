<h4>Краткая справка</h4>
<p>Раздел предназначен для редактирования содержания медицинской карты для рабочего места врача. Карта у врача разбита на категории (раскрывающиеся списки), внутри них имеются управляющие элементы, которые могут представлять собой, в том числе, выбор значения из справочника.
    При формировании шаблона карты требуется определить группы, поля карты, справочники и привязать последние к определённым полям. Справочники при необходимости можно дополнять значениями.
</p>
<?php $this->widget('application.components.widgets.DoctorCardTabMenu') ?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/categories.js"></script>
<table id="categories"></table>
<div id="categoriesPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addCategorie">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editCategorie">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default" id="deleteCategorie">Удалить запись</button>
</div>
<div class="modal fade" id="addCategoriePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить категорию</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'categorie-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/categories/add'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'name', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'name', array(
                                    'id' => 'name',
                                    'class' => 'form-control',
                                    'placeholder' => 'Название категории'
                                )); ?>
                                <?php echo $form->error($model,'name'); ?>
                            </div>
                        </div>
						<div class="form-group">
                            <?php echo $form->labelEx($model,'parentId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model,'parentId', $categoriesList, array(
                                    'id' => 'parentId',
                                    'class' => 'form-control',
                                    'placeholder' => 'Категория-родитель'
                                )); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'isDynamic', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model,'isDynamic', array('Нет', 'Да'), array(
                                    'id' => 'isDynamic',
                                    'class' => 'form-control'
                                )); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'position', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'position', array(
                                    'id' => 'position',
                                    'class' => 'form-control'
                                )); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'isWrapped', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model,'isWrapped', array('Нет', 'Да'), array(
                                    'id' => 'isWrapped',
                                    'class' => 'form-control'
                                )); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/categories/add'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#categorie-add-form").trigger("success", [data, textStatus, jqXHR])
                            }'
                    ),
                    array(
                        'class' => 'btn btn-primary'
                    )
                ); ?>
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>
<div class="modal fade" id="editCategoriePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать категорию</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'name'),
                'id' => 'categorie-edit-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/categories/edit'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <?php echo $form->hiddenField($model,'id', array(
                                'id' => 'id',
                                'class' => 'form-control'
                            )); ?>
                            <?php echo $form->labelEx($model,'name', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'name', array(
                                    'id' => 'name',
                                    'class' => 'form-control',
                                    'placeholder' => 'Название категории'
                                )); ?>
                                <?php echo $form->error($model,'name'); ?>
                            </div>
                        </div>
						<div class="form-group">
                            <?php echo $form->labelEx($model,'parentId', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model,'parentId', $categoriesList, array(
                                    'id' => 'parentId',
                                    'class' => 'form-control',
                                    'placeholder' => 'Категория-родитель'
                                )); ?>
                                <?php echo $form->error($model,'parentId'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'isDynamic', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model,'isDynamic', array('Нет', 'Да'), array(
                                    'id' => 'isDynamic',
                                    'class' => 'form-control'
                                )); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'position', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'position', array(
                                    'id' => 'position',
                                    'class' => 'form-control'
                                )); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'isWrapped', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList($model,'isWrapped', array('Нет', 'Да'), array(
                                    'id' => 'isWrapped',
                                    'class' => 'form-control'
                                )); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Сохранить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/admin/categories/edit'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#categorie-edit-form").trigger("success", [data, textStatus, jqXHR])
                            }'
                    ),
                    array(
                        'class' => 'btn btn-primary'
                    )
                ); ?>
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="errorAddCategoriePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Ошибка!</h4>
            </div>
            <div class="modal-body">
                <h4>При заполнении формы возникли следующие ошибки:</h4>
                <div class="row">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>