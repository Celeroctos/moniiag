<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/reportForDay.js"></script>
<h4>Отчёт за день</h4>
<div class="row">
    <form method="post" id="reception-shedule-form" role="form" class="form-horizontal col-xs-12">
        <div class="col-xs-5">
            <div class="form-group">
                <label class="col-xs-2 control-label required" for="reportDate">Дата</label>
                <div class="col-xs-3 input-group date date-control" id="reportDate-cont">
                    <input type="hidden" id="reportDate" class="form-control col-xs-4" placeholder="Формат гггг-мм-дд" name="reportDate">
				  <span class="input-group-addon">
					  <span class="glyphicon-calendar glyphicon">
					  </span>
				  </span>
                    <div class="subcontrol">
                        <div class="date-ctrl-up-buttons">
                            <div class="btn-group">
                                <button class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-day-button" tabindex="-1" type="button"></button>
                                <button class="btn btn-default btn-xs glyphicon-arrow-up glyphicon month-button up-month-button" tabindex="-1" type="button"></button>
                                <button class="btn btn-default btn-xs glyphicon-arrow-up glyphicon year-button up-year-button" tabindex="-1" type="button"></button>
                            </div>
                        </div>
                        <div class="form-inline subfields">
                            <input type="text" class="form-control day" placeholder="ДД" name="day">
                            <input type="text" class="form-control month" placeholder="ММ" name="month">
                            <input type="text" class="form-control year" placeholder="ГГГГ" name="year">
                        </div>
                        <div class="date-ctrl-down-buttons">
                            <div class="btn-group">
                                <button class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-day-button" tabindex="-1" type="button"></button>
                                <button class="btn btn-default btn-xs glyphicon-arrow-down glyphicon month-button down-month-button" tabindex="-1" type="button"></button>
                                <button class="btn btn-default btn-xs glyphicon-arrow-down glyphicon year-button down-year-button" tabindex="-1" type="button"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-success" id="reportForDayViewSubmit">Посмотреть отчёт</button>
                <button type="button" class="btn btn-success" id="reportForDayViewPrint">Печать</button>
            </div>
        </div>
    </form>
</div>
<table id="workForDay"></table>
<div id="workForDayPager"></div>