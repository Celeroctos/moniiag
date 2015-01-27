<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/tasu.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/progressbar.js" ></script>
<h4>Инструменты обсулуживания связки МИС <-> ТАСУ</h4>
<p>Раздел предлагает инструменты обслуживания связки МИС <-> ТАСУ (Типовой Автоматизированной Системой Управления).</p>
<?php $this->widget('application.modules.admin.components.widgets.UploadTasuTypesTabMenu', array(
));
?>
<h4>Синхронизация с базой данных ТАСУ (подключение <span class="connectionString"><?php echo Yii::app()->db2->connectionString; ?>)</span> и
    синхронизация с базой данных ТАСУ-ОМС (подключение <span class="connectionString"><?php echo Yii::app()->db3->connectionString; ?>)</span></h4>
<div class="row">
    <div id="accordionA" class="accordion">
        <div class="accordion-group">
            <div class="accordion-heading">
                <a href="#collapseA" data-parent="#accordionA" data-toggle="collapse" class="accordion-toggle" data-toggle="tooltip" data-placement="right" title="Создание поискового поля для полисов"><strong>Создание поискового поля для полисов</strong></a>
            </div>
            <div class="accordion-body collapse in" id="collapseA">
                <div class="accordion-inner">
                    <div class="row default-padding-left">
                        <p>Данная функциональность обеспечивает создание поискового поля в таблице полисов.</p>
                        <input type="button" class="btn btn-success syncBtn" value="Сгенерировать" />
                    </div>
                    <div class="row borderedBox default-margin-top progressBox no-display default-margin-right" id="genPoliciesOnlySymbols">
                        <h5><strong>Прогресс генерации</strong></h5>
                        <div class="progress progress-striped active">
                            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                        <p class="text-warning">Всего строк: <span class="numStringsAll">0</span></p>
                        <p class="text-primary">Обработано строк: <span class="numStrings">0</span></p>
                        <p class="text-success">Добавлено строк: <span class="numStringsAdded">0</span></p>
                        <p class="text-danger"><strong>Ошибок (строк): <span class="numStringsError">0</span></strong></p>
                        <div class="form-group clear">
                            <input type="button" class="btn btn-success successImport no-display" value="Закончить генерацию">
                            <input type="button" class="btn btn-danger pauseImport" value="Пауза">
                            <input type="button" class="btn btn-danger continueImport" value="Продолжить" disabled="disabled">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>