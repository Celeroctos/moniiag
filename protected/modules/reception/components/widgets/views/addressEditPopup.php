<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'address-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
));
?>
<div class="modal fade error-popup" id="editAddressPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактирование адреса</h4>
            </div>
            <div class="modal-body">
                <div class="form-group chooser" id="regionChooser">
                    <label for="region" class="col-xs-4 control-label">Регион (Enter - добавить)</label>
                    <div class="col-xs-7">
                        <div class="input-group">
                            <input type="text" class="form-control" id="region" placeholder="Регион">
                            <span class="input-group-addon glyphicon glyphicon-plus"></span>
                        </div>
                        <ul class="variants no-display">
                        </ul>
                        <div class="choosed">
                        </div>
                    </div>
                </div>
                <div class="form-group chooser" id="districtChooser">
                    <label for="district" class="col-xs-4 control-label">Район (Enter - добавить)</label>
                    <div class="col-xs-7">
                        <div class="input-group">
                            <input type="text" class="form-control" id="district" placeholder="Район" >
                            <span class="input-group-addon glyphicon glyphicon-plus"></span>
                        </div>
                        <ul class="variants no-display">
                        </ul>
                        <div class="choosed">
                        </div>
                    </div>
                </div>
                <div class="form-group chooser" id="settlementChooser">
                    <label for="settlement" class="col-xs-4 control-label">Населённый пункт (Enter - добавить)</label>
                    <div class="col-xs-7">
                        <div class="input-group">
                            <input type="text" class="form-control" id="settlement" placeholder="Населённый пункт">
                            <span class="input-group-addon glyphicon glyphicon-plus"></span>
                        </div>
                        <ul class="variants no-display">
                        </ul>
                        <div class="choosed">
                        </div>
                    </div>
                </div>
                <div class="form-group chooser" id="streetChooser">
                    <label for="street" class="col-xs-4 control-label">Улица (Enter - добавить)</label>
                    <div class="col-xs-7">
                        <div class="input-group">
                            <input type="text" class="form-control" id="street" placeholder="Улица">
                            <span class="input-group-addon glyphicon glyphicon-plus"></span>
                        </div>
                        <ul class="variants no-display">
                        </ul>
                        <div class="choosed">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="house" class="col-xs-4 control-label">Дом</label>
                    <div class="col-xs-7">
                        <input type="text" class="form-control" id="house" placeholder="Дом">
                    </div>
                </div>
                <div class="form-group">
                    <label for="building" class="col-xs-4 control-label">Корпус</label>
                    <div class="col-xs-7">
                        <input type="text" class="form-control" id="building" placeholder="Корпус">
                    </div>
                </div>
                <div class="form-group">
                    <label for="flat" class="col-xs-4 control-label">Квартира</label>
                    <div class="col-xs-7">
                        <input type="text" class="form-control" id="flat" placeholder="Квартира">
                    </div>
                </div>
                <div class="form-group">
                    <label for="postindex" class="col-xs-4 control-label">Почтовый индекс</label>
                    <div class="col-xs-7">
                        <input type="text" class="form-control" id="postindex" placeholder="Почтовый индекс">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-success editSubmit">Сохранить адрес</button>
            </div>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>
<div class="modal fade error-popup" id="addNewCladrRegion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавление региона</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/cladr/regionadd'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#region-add-form").trigger("success", [data, textStatus, jqXHR])
                            }'
                    ),
                    array(
                        'class' => 'btn btn-primary'
                    )
                ); ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="addNewCladrDistrict">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавление района</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/cladr/districtadd'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#district-add-form").trigger("success", [data, textStatus, jqXHR])
                            }',
                        'beforeSend' => 'function(jqXHR, settings) {
                             $("#district-add-form").trigger("beforesend", [settings, jqXHR])
                        }'
                    ),
                    array(
                        'class' => 'btn btn-primary'
                    )
                ); ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="addNewCladrSettlement">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавление населённого пункта</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/cladr/settlementadd'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#settlement-add-form").trigger("success", [data, textStatus, jqXHR])
                        }',
                        'beforeSend' => 'function(jqXHR, settings) {
                             $("#settlement-add-form").trigger("beforesend", [settings, jqXHR])
                        }'
                    ),
                    array(
                        'class' => 'btn btn-primary'
                    )
                ); ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="addNewCladrStreet">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавление улицы</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/cladr/streetadd'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#street-add-form").trigger("success", [data, textStatus, jqXHR])
                        }',
                        'beforeSend' => 'function(jqXHR, settings) {
                             $("#street-add-form").trigger("beforesend", [settings, jqXHR])
                        }'
                    ),
                    array(
                        'class' => 'btn btn-primary'
                    )
                ); ?>
            </div>
        </div>
    </div>
</div>