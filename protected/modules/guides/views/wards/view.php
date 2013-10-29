<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/wards.js"></script>
<table id="wards">
</table>
<div id="wardsPager">
</div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-default" id="addWard">Добавить запись</button>
    <button type="button" class="btn btn-default" id="editWard">Редактировать выбранную запись</button>
    <button type="button" class="btn btn-default" id="deleteWard">Удалить выбранные</button>
</div>
<div class="modal fade" id="addWardPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить отделение</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal col-xs-12" role="form">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label for="middleName" class="col-xs-3 control-label">Название отделения</label>
                                <div class="col-xs-9">
                                    <input type="text" class="form-control" id="middleName" placeholder="Название отделения">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="post" class="col-xs-3 control-label">Учреждение</label>
                                <div class="col-xs-9">
                                    <select class="form-control" id="post">
                                        <option>МОНИИАГ</option>
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

