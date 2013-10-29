<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/enterprises.js"></script>
<table id="enterprises"></table>
<div id="enterprisesPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addEnterprise">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editEnterprise">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default" id="deleteEnterprise">Удалить выбранные</button>
</div>
<div class="modal fade" id="addEnterprisePopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить учреждение</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal col-xs-12" role="form">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label for="shortName" class="col-xs-3 control-label">Краткое название</label>
                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="shortName" placeholder="Краткое название">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="fullName" class="col-xs-3 control-label">Полное название</label>
                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="fullName" placeholder="Полное название">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="addressFact" class="col-xs-3 control-label">Адрес фактический</label>
                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="addressFact" placeholder="Адрес фактический">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="addressJur" class="col-xs-3 control-label">Адрес юридический</label>
                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="addressJur" placeholder="Адрес юридический">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-xs-3 control-label">Телефон</label>
                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="phone" placeholder="Телефон">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="bank" class="col-xs-3 control-label">Банк</label>
                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="bank" placeholder="Банк">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="bankAccount" class="col-xs-3 control-label">Расчётный счёт</label>
                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="bankAccount" placeholder="Расчётный счёт">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inn" class="col-xs-3 control-label">ИНН</label>
                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="inn" placeholder="ИНН">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="kpp" class="col-xs-3 control-label">КПП</label>
                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="kpp" placeholder="КПП">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="post" class="col-xs-3 control-label">Тип</label>
                                <div class="col-xs-9">
                                    <select class="form-control" id="type">
                                        <option>Поликлиника</option>
                                        <option>Стационар</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary">Добавить</button>
            </div>
        </div>
    </div>
</div>
