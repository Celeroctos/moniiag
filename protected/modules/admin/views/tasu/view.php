<link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/fileuploader/style.css" rel="stylesheet" type="text/css" media="screen"  />
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/fileuploader/script.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/tasu.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/fileuploader.js" ></script>

<h4>Инструменты интеграции с ТАСУ</h4>
<p>Раздел предлагает инструменты управления интеграцией с ТАСУ (Типовой Автоматизированной Системой Управления) ОМС.</p>
<h4>Загрузка данных об ОМС</h4>
<div class="row">
    <form class="form-horizontal col-xs-12" role="form" id="tasu-upload-form">
        <div class="fileinput-group" id="tasuIn">
            <div class="fileinput-wrap">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <span class="btn btn-default btn-file">
                        <span class="fileinput-new">Выберите файл...</span>
                        <span class="fileinput-exists">Перевыбрать</span>
                        <input type="file" name="...">
                    </span>
                    <span class="fileinput-filename"></span>
                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                    <div class="progress progress-striped active no-display">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                            <span class="sr-only">0% завершено</span>
                        </div>
                        <span class="description">0% завершено</span>
                        <span class="text-success ok no-display">
                            <span class="glyphicon glyphicon-chevron-down"></span>
                        </span>
                    </div>
                    <button class="btn btn-default btn-sm plus no-display" type="button">
                        <span class="glyphicon glyphicon-plus"></span>
                    </button>
                </div>
            </div>
            <div>
                <input type="button" value="Загрузить" class="btn btn-success submit" >
            </div>
        </div>
    </form>
</div>
<div class="modal fade error-popup" id="errorPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Ошибка!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Произошла ошибка при загрузке файла.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>