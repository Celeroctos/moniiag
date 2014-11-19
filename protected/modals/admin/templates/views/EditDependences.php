<div class="modal-body">
    <input id="valuesNotToPrint" type="hidden">
    <div class="row">
        <div class="col-xs-5" id="controlValuesPanel">
            <h5>Значения выбранного списка</h5>
            <div class="row">
                <select id="controlValues" multiple="multiple" class="form-control">
                </select>
                <span class="notPrintIfThisValueContainer no-display">
                    <input type="checkbox" id="notPrintIfThisValue">Не печатать при выборе данного значения
                </span>
            </div>
            <!--div class="row"></div-->
        </div>
        <div class="col-xs-5 no-display" id="controlDependencesPanel">
            <h5>Список элементов управления</h5>
            <div class="row">
                <select id="controlDependencesList" multiple="multiple" class="form-control">
                </select>
            </div>
            <h5 class="no-display">Действие</h5>
            <div class="row no-display">
                <select id="controlActions" class="form-control">
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <table id="dependences"></table>
        <div id="dependencesPager"></div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-success no-display" id="saveDependencesBtn">Сохранить</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>