<h4>Краткая справка</h4>
<p>Раздел предназначен для редактирования "любимых" диагнозов для медкарты. При приёме пациента врач ставит два диагноза: основной и сопутствующий. Оба эти диагноза могут выбираться из особых справочников в разрезе специальности. Ниже Вы можете задать эти справочники для каждой специальности.
</p>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/diagnosis.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/mkb10.js"></script>
<table id="diagnosiss"></table>
<div id="diagnosissPager"></div>
<div class="btn-group default-margin-top">
    <button type="button" class="btn btn-success" id="editDiagnosis">Редактировать диагнозы</button>
</div>
<div class="modal fade error-popup" id="editPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактировать любимые диагнозы специальности <span class="spec"></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Для добавления диагноза щёлкните два раза на записи в нижней таблице. Для удаления диагноза из списка щёлкните два раза по строке верхней таблицы. После всех изменений нажмите кнопку "Сохранить изменения".</p>
                    <div>
                        <table id="diagnosisList"></table>
                        <div id="diagnosisListPager"></div>
                    </div>
                    <div class="second">
                        <table id="mkb10"></table>
                        <div id="mkb10Pager"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="likeDiagnosisSubmit">Сохранить изменения</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade error-popup" id="successPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Успешно!</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>