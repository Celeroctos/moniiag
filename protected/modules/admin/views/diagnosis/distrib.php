<h4>Краткая справка: "Диагнозы для распределения больных"</h4>
<p>Раздел предназначен для редактирования выделения среди "любимых" диагнозов тех, по которым можно искать подходящего врача при записи пациента.</p>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/diagnosis_distrib.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/guides/mkb10.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/chooser.js"></script>
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
                <h4 class="modal-title">Редактировать диагнозы для распределения больных специальности <span class="spec"></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>Для добавления диагноза щёлкните два раза на записи с иконкой круга в нижней таблице, либо вводите название диагноза в текстовое поле ниже. Для удаления диагноза из списка щёлкните по красному кресту рядом с диагнозом. После всех изменений нажмите кнопку "Сохранить изменения".</p>
                    <div class="second">
                        <div class="form-group chooser first" id="diagnosisChooser">
                            <div class="col-xs-4">
                                <input type="text" class="form-control" autofocus id="diagnosis" placeholder="Название диагноза">
                                <ul class="variants no-display">
                                </ul>
                                <div class="choosed">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="first">
                        <div class="form-group chooser first" id="diagnosisDistribChooser">
                            <div class="col-xs-4">
                                <input type="text" class="form-control" autofocus id="diagnosisDistrib" placeholder="Название диагноза">
                                <ul class="variants no-display">
                                </ul>
                                <div class="choosed">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="distribDiagnosisSubmit">Сохранить изменения</button>
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