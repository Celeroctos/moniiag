<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-json.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/admin/tasuimport.js"></script>
<h4>Импорт приёмов врачей в ТАСУ</h4>
<div class="row importTable">
	<table id="greetings"></table>
	<div id="greetingsPager"></div>
	<div class="btn-group default-margin-top">
		<button type="button" class="btn btn-default" id="addGreeting">Добавить запись</button>
		<button type="button" class="btn btn-default" id="editGreeting">Редактировать выбранную запись</button>
		<button type="button" class="btn btn-default" id="deleteGreeting">Удалить выбранные</button>
		<button type="button" class="btn btn-default" id="importGreetings">Выгрузить</button>
		<button type="button" class="btn btn-default" id="clearGreetings">Очистить</button>
	</div>
</div>
<div class="row borderedBox default-margin-top progressBox">
	<h5><strong>Прогресс импорта</strong></h5>
	<div class="progress progress-striped active">
		<div class="progress-bar progress-bar-warning" id="importProgressbarP" role="progressbar" aria-valuenow="37" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
			<span class="sr-only"></span>
		</div>
	</div>
	<p class="text-warning">Всего приёмов (строк): <span class="numStringsAll">0</span></p>
	<p class="text-primary">Обработано приёмов (строк): <span class="numStrings">0</span></p>
	<p class="text-success">Добавлено (строк): <span class="numStringsAdded">0</span></p>
	<p class="text-danger">Отклонено (строк): <span class="numStringsDiscarded">0</span></p>
	<p class="text-danger"><strong>Ошибок (строк): <span class="numStringsError">0</span></strong></p>
	<p class="text-success"><strong>Добавлено (медкарт): <span class="numMedcardsAdded">0</span></strong></p>
	<p class="text-success"><strong>Добавлено (пациентов): <span class="numPatientsAdded">0</span></strong></p>
	<div class="form-group clear">
		<input type="button" class="btn btn-success successImport no-display" value="Закончить импорт">
		<input type="button" class="btn btn-danger pauseImport" value="Пауза">
		<input type="button" class="btn btn-danger continueImport disabled" value="Продолжить">
	</div>
</div>
<h4>Лог выгрузки</h4>
<div class="row logWindow">
	<ul class="list-group">
	  <li class="list-group-item">Cras justo odio</li>
	  <li class="list-group-item">Dapibus ac facilisis in</li>
	  <li class="list-group-item">Morbi leo risus</li>
	  <li class="list-group-item">Porta ac consectetur ac</li>
	  <li class="list-group-item">Vestibulum at eros</li>
	</ul>
</div>      
<h4>История выгрузок</h4>
<div class="row importHistoryTable">
	<table id="importHistory"></table>
	<div id="importHistoryPager"></div>
</div>