<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/writePatient.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js" ></script>
<?php if(Yii::app()->user->checkAccess('writePatient')) { ?>
<div class="row">
    <?php $this->widget('application.modules.reception.components.widgets.WritePatientTabMenu'); ?>
</div>
<h4>Запись пациента</h4>
<p class="text-left">
    Шаг 1. Найдите пациента с помощью формы ниже. Шаг 2. Выберите, к какому врачу записать пациента и на какое время, нажав на иконку часов в строке таблицы рядом с пациентом.
</p>
<h4>Шаг 1. Найти пациента</h4>
<p class="text-left">
    Задайте условия поиска:
</p>
<div class="row">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'patient-search-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/search'),
        'htmlOptions' => array(
            'class' => 'form-horizontal col-xs-12',
            'role' => 'form'
        )
    ));
    ?>
    <div class="form-group">
        <label for="omsNumber" class="col-xs-2 control-label">Номер ОМС</label>
        <div class="col-xs-4">
            <input type="text" class="form-control" autofocus id="omsNumber" placeholder="ОМС">
        </div>
    </div>
    <div class="form-group">
        <label for="cardNumber" class="col-xs-2 control-label">Номер карты</label>
        <div class="col-xs-4">
            <input type="text" class="form-control" id="cardNumber" placeholder="Номер карты">
        </div>
    </div>
    <div class="form-group">
        <label for="lastName" class="col-xs-2 control-label">Фамилия</label>
        <div class="col-xs-4">
            <input type="text" class="form-control" id="lastName" placeholder="Фамилия">
        </div>
    </div>
    <div class="form-group">
        <label for="firstName" class="col-xs-2 control-label">Имя</label>
        <div class="col-xs-4">
            <input type="text" class="form-control" id="firstName" placeholder="Имя">
        </div>
    </div>
    <div class="form-group">
        <label for="middleName" class="col-xs-2 control-label">Отчество</label>
        <div class="col-xs-4">
            <input type="text" class="form-control" id="middleName" placeholder="Отчество">
        </div>
    </div>
    <div class="form-group">
        <label for="serie" class="col-xs-2 control-label">Серия документа</label>
        <div class="col-xs-4">
            <input type="text" class="form-control" id="serie" placeholder="Серия документа">
        </div>
    </div>
      <div class="form-group">
        <label for="docnumber" class="col-xs-2 control-label">Номер документа</label>
        <div class="col-xs-4">
            <input type="text" class="form-control" id="docnumber" placeholder="Номер документа">
        </div>
    </div>
    <div class="form-group">
        <label for="addressReg" class="col-xs-2 control-label">Адрес регистрации</label>
        <div class="col-xs-4">
            <input type="text" class="form-control" id="addressReg" placeholder="Адрес регистрации">
        </div>
    </div>
    <div class="form-group">
        <label for="address" class="col-xs-2 control-label">Адрес прописки</label>
        <div class="col-xs-4">
            <input type="text" class="form-control" id="address" placeholder="Адрес прописки">
        </div>
    </div>
     <div class="form-group">
        <label for="snils" class="col-xs-2 control-label">СНИЛС</label>
        <div class="col-xs-4">
            <input type="text" class="form-control" id="snils" placeholder="Формат XXX-XXX-XXX-XX">
        </div>
    </div>
    <div class="form-group">
        <label for="birthday" class="col-xs-2 control-label required">Дата рождения</label>
        <div id="birthday-cont" class="col-xs-3 input-group date">
            <input type="hidden" name="birthday" placeholder="Формат гггг-мм-дд" class="form-control col-xs-4" id="birthday">
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
        <input type="button" id="patient-search-submit" value="Найти" name="patient-search-submit" class="btn btn-success">
    </div>
    <?php $this->endWidget(); ?>
</div>
<h4>Список пациентов по поисковому запросу</h4>
<p class="text-left">
    В таблице отображаются результаты поискового запроса.
</p>
<div class="row">
    <div class="col-xs-12 borderedBox">
        <table class="table table-condensed table-hover" id="searchWithCardResult">
            <thead>
            <tr class="header">
                <td>
                    ФИО пациента
                </td>
		<td>
		    Дата рождения
		</td>
                <td>
                    Номер карты
                </td>
                <td>
                    Номер ОМС
                </td>
                <td>
                    Записать на приём
                </td>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="row no-display">
	<ul class="pagination content-pagination">
	</ul>
    </div>
</div>
<?php } ?>
<div class="modal fade error-popup" id="errorSearchPopup">
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
<div class="modal fade error-popup" id="notFoundPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Сообщение</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p>По введённым поисковым критериям не найдено ни одного пациента. Вы можете ввести новые данные о пациенте, перейдя по <?php echo CHtml::link('этой', array('/reception/patient/viewadd')) ?> ссылке.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
