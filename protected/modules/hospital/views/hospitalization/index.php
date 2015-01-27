<div class="col-xs-12 row">
	<ul class="nav nav-tabs" id="hospitalizationNavbar">
	  <li role="navigation" class="active">
		<a href="#queue" aria-controls="queue" role="tab" data-toggle="tab">Очередь</a>
		<span class="roundedLabel"></span>
		<span class="roundedLabelText">1</span>
	  </li>
	  <li role="navigation">
		<a href="#comission" aria-controls="comission" role="tab" data-toggle="tab">Комиссия на госпитализацию</a>
	  </li>
	  <li role="navigation">
		<a href="#hospitalization" aria-controls="hospitalization" role="tab" data-toggle="tab">Госпитализация</a>
	  </li>
	  <li role="navigation">
		<a href="#history" aria-controls="history" role="tab" data-toggle="tab">История приёмов</a>
	  </li>
	</ul>
</div>
<div class="row col-xs-12 tableBlock">
	<div class="hospitalizationSide col-xs-3">
		<div id="sideCalendar"></div>
	</div>
	<div class="hospitalizationTable col-xs-8">
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="queue">
				<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/ajax-loader2.gif" width="256" height="30" alt="Загружается..." class="ajaxLoader" />
			</div>
			<div role="tabpanel" class="tab-pane" id="comission">
				<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/ajax-loader2.gif" width="256" height="30" alt="Загружается..." class="ajaxLoader" />
			</div>
			<div role="tabpanel" class="tab-pane" id="hospitalization">
				<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/ajax-loader2.gif" width="256" height="30" alt="Загружается..." class="ajaxLoader" />
			</div>
			<div role="tabpanel" class="tab-pane" id="history">
				<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/ajax-loader2.gif" width="256" height="30" alt="Загружается..." class="ajaxLoader" />
			</div>
		</div>
	</div>
</div>