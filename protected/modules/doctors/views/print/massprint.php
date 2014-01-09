<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/doctors/print.js"></script>
<h3>Массовая печать приёмов для медицинских карт</h3>
<p class="text-left">
    С помощью элементов управления, расположенных ниже, Вы можете выбрать врачей, для которых необходимо напечатать определённые записи медицинской карты, а также сами записи.
</p>
<form class="form-horizontal col-xs-12" role="form" id="print-search-form" method="post">
    <div class="form-group chooser" id="doctorChooser">
        <label for="doctor" class="col-xs-2 control-label">Врач (Enter - добавить)</label>
        <div class="col-xs-4">
            <input type="text" class="form-control" autofocus id="doctor" placeholder="ФИО врача">
            <ul class="variants no-display">
            </ul>
            <div class="choosed">
            </div>
        </div>
    </div>
    <div class="form-group chooser" id="patientChooser">
        <label for="categorie" class="col-xs-2 control-label">Пациент (Enter - добавить)</label>
        <div class="col-xs-4">
            <input type="text" class="form-control" autofocus id="patient" placeholder="ФИО пациента">
            <ul class="variants no-display">
            </ul>
            <div class="choosed">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="greetingDate" class="col-xs-2 control-label required">Дата приёма</label>
        <div id="greetingDate-cont" class="col-xs-3 input-group date">
            <input type="hidden" name="birthday" placeholder="Формат гггг-мм-дд" class="form-control col-xs-4" id="greetingDate">
			<span class="input-group-addon">
            	<span class="glyphicon-calendar glyphicon">
				</span>
            </span>
            <div class="subcontrol">
                <div class="date-ctrl-up-buttons">
                    <div class="btn-group">
                        <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-day-button"></button>
                        <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon month-button up-month-button"></button>
                        <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon year-button up-year-button" ></button>
                    </div>
                </div>
                <div class="form-inline subfields">
                    <input type="text" name="day" placeholder="ДД" class="form-control day">
                    <input type="text" name="month" placeholder="ММ" class="form-control month">
                    <input type="text" name="year" placeholder="ГГГГ" class="form-control year">
                </div>
                <div class="date-ctrl-down-buttons">
                    <div class="btn-group">
                        <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-day-button"></button>
                        <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon month-button down-month-button"></button>
                        <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon year-button down-year-button" ></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="print-submit">
            <button type="button" class="btn btn-success">Сформировать список на печать</button>
        </div>
    </div>
</form>
<div class="row no-display" id="massPrintDocs">
    <h5><strong>Найденные приёмы для печати:</strong> (нажмите <a href="#" id="massPrintAllPerList">сюда</a>, чтобы распечатать все найденные результаты на одном листе с разделителями)</h5>
    <div class="col-xs-12 borderedBox">
        <table class="table table-condensed table-hover">
            <thead>
            <tr class="header">
                <td>
                    Дата
                </td>
                <td>
                    Пациент
                </td>
                <td>
                    Врач
                </td>
                <td>
                </td>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
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