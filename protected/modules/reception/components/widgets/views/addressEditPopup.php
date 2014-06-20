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
<script type="text/javascript" src="/js/addressEdit.js"></script>
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
                            <span class="input-group-addon glyphicon glyphicon-plus addNewRegionButton"></span>
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
                            <span class="input-group-addon glyphicon glyphicon-plus addNewDistrictButton"></span>
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
                            <span class="input-group-addon glyphicon glyphicon-plus addNewSettlementButton"></span>
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
                            <span class="input-group-addon glyphicon glyphicon-plus addNewStreetButton"></span>
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
<?php
$this->widget('application.modules.guides.components.widgets.claddr.regionAdder', array(
));
?>
<?php
$this->widget('application.modules.guides.components.widgets.claddr.settlementAdder', array(
));
?>
<?php
$this->widget('application.modules.guides.components.widgets.claddr.streetAdder', array(
));
?>
<?php
$this->widget('application.modules.guides.components.widgets.claddr.districtAdder', array(
));
?>