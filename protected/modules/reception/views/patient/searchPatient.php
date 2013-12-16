<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/searchAddPatient.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js" ></script>
<h4>Поиск пациента по ОМС</h4>
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
		<div class="col-xs-2">
		</div>
		<!--Выводим верхние кнопочки для увеличения значений даты-->
		<div class="btn-group col-xs-4">
    		<button type="button" tabindex = "-1" class="btn btn-default btn-xs glyphicon-plus glyphicon year-button up-year-button" >
				<!--<span class="glyphicon-arrow-up glyphicon">-->
			<!--	</span>-->
			</button>
     		<button type="button" tabindex = "-1" class="btn btn-default btn-xs glyphicon-plus glyphicon month-button up-month-button">
				<!--<span class="glyphicon-arrow-up glyphicon">-->
			<!--	</span>-->
			</button>
    		<button type="button" tabindex = "-1" class="btn btn-default btn-xs glyphicon-plus glyphicon up-day-button">
				<!--<span class="glyphicon-arrow-up glyphicon">-->
			<!--	</span>-->
			</button>
		</div>
	</div>
	<div class="form-group">
        <label for="birthday" class="col-xs-2 control-label required">Дата рождения</label>   
    	<div id="birthday-cont" class="col-xs-4 input-group date">
            <input type="text" name="birthday" placeholder="Формат гггг-мм-дд" class="form-control col-xs-4" id="birthday">
			<span class="input-group-addon">
            	<span class="glyphicon-calendar glyphicon">
				</span>
            </span>
    	</div>
    </div>
	<div  class="form-group">
		<div class="col-xs-2">
		</div>		
		<div class="btn-group col-xs-4">	
		<!--Выводим нижние кнопочки для уменьшения значений даты-->
    		<button type="button" tabindex = "-1" class="btn btn-default btn-xs glyphicon-minus glyphicon year-button down-year-button">
				<!--<span class="glyphicon-arrow-down glyphicon">-->
				<!--</span>-->
			</button>
     		<button type="button" tabindex = "-1"  class="btn btn-default btn-xs glyphicon-minus glyphicon month-button down-month-button">
			<!--	<span class="glyphicon-arrow-down glyphicon">-->
				<!--</span>-->
			</button>
    		<button type="button" tabindex = "-1"  class="btn btn-default btn-xs glyphicon-minus glyphicon down-day-button">
			<!--	<span class="glyphicon-arrow-down glyphicon">-->
				<!--</span>-->
			</button>
		</div>
	</div>
    
    
    <!--
       <div class="form-group">
		<div class="col-xs-2">
		</div>

		<div class="btn-group col-xs-4">
    		<button type="button" class="btn btn-default btn-xs year-button up-year-button" >
				<span class="glyphicon-arrow-up glyphicon">
				</span>
			</button>
     		<button type="button" class="btn btn-default btn-xs month-button up-year-button">
				<span class="glyphicon-arrow-up glyphicon">
				</span>
			</button>
    		<button type="button" class="btn btn-default btn-xs up-year-button">
				<span class="glyphicon-arrow-up glyphicon">
				</span>
			</button>
		</div>
	</div>
	<div class="form-group">
        <label for="birthday" class="col-xs-2 control-label required">Дата рождения</label>   
    	<div id="birthday-cont1" class="col-xs-4 input-group date">
            <input type="text" name="birthday" placeholder="Формат гггг-мм-дд" class="form-control col-xs-4" id="birthday">
			<span class="input-group-addon">
            	<span class="glyphicon-calendar glyphicon">
				</span>
            </span>
    	</div>
    </div>
	<div  class="form-group">
		<div class="col-xs-2">
		</div>		
		<div class="btn-group col-xs-4">	

    		<button type="button" class="btn btn-default btn-xs year-button down-year-button">
				<span class="glyphicon-arrow-down glyphicon">
				</span>
			</button>
     		<button type="button" class="btn btn-default btn-xs month-button down-month-button">
				<span class="glyphicon-arrow-down glyphicon">
				</span>
			</button>
    		<button type="button" class="btn btn-default btn-xs down-day-button">
				<span class="glyphicon-arrow-down glyphicon">
				</span>
			</button>
		</div>
	</div>
    
    -->

    <div class="form-group">
        <input type="button" id="patient-search-submit" value="Найти" name="patient-search-submit" class="btn btn-success">
    </div>
    <?php $this->endWidget(); ?>
</div>
<div class="row no-display" id="withoutCardCont">
    <h5>Найденные пациенты без карт:</h5>
    <div class="col-xs-12 borderedBox">
        <table class="table table-condensed table-hover" id="omsSearchWithoutCardResult">
            <thead>
                <tr class="header">
                    <td>
                        ФИО
                    </td>
                    <td>
                        Номер ОМС
                    </td>
                    <td>
                        Регистрировать ЭМК
                    </td>
                    <td>
                        Редактировать ОМС
                    </td>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<div class="row no-display" id="withCardCont">
    <h5>Найденные пациенты с картами:</h5>
    <div class="col-xs-12 borderedBox">
        <table class="table table-condensed table-hover" id="omsSearchWithCardResult">
            <thead>
            <tr class="header">
                <td>
                    ФИО
                </td>
                <td>
                    Номер ОМС
                </td>
                <td>
                    Год регистрации карты
                </td>
                <td>
                    Номер карты
                </td>
                <td>
                    Перерегистрировать ЭМК
                </td>
                <td>
                    Редактировать ЭМК
                </td>
                <td>
                    Редактировать ОМС
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
</div>
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