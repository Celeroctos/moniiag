<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/template-engine.css">
<div class="modal-body">
    <div class="row">
		<div class="template-engine-widget"></div>
    </div>
</div>
<div class="modal-footer">
    <img src="<?=Yii::app()->baseUrl?>/images/ajax-loader.gif" width="30" class="saving-template" style="margin-right: 20px">
	<button type="button" class="btn btn-primary" id="saveTemplateButton" data-loading-text="Сохранение...">Сохранить</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>