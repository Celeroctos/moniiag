<div class="col-xs-12 row">
	<ul class="nav nav-tabs" id="hospitalizationNavbar">
	  <li role="navigation" class="active">
		<a href="#">Очередь</a>
		<span class="roundedLabel"></span>
		<span class="roundedLabelText">1</span>
	  </li>
	  <li role="navigation">
		<a href="#">Комиссия на госпитализацию</a>
	  </li>
	  <li role="navigation">
		<a href="#">Госпитализация</a>
	  </li>
	  <li role="navigation">
		<a href="#">История приёмов</a>
	  </li>
	</ul>
</div>
<div class="row col-xs-12 tableBlock">
	<div class="hospitalizationSide col-xs-3">
		<div id="sideCalendar"></div>
	</div>
	<div class="hospitalizationTable col-xs-8">
		<?php
			$this->widget('zii.widgets.grid.CGridView', array(
				'dataProvider' => $dataProvider,
				'enablePagination' => true,
				'summaryCssClass' => 'summaryPanel',
				'id' => 'hospitalizationSummary', 
				'columns' => array(
					array(
						'name' => 'id',
						'type' => 'raw',
						'value' => '$data->id',
					),
					array(
						'name' => 'last_name',
						'type' => 'raw',
						'value' => '$data->last_name',
					),
					array(
						'name' => 'first_name',
						'type' => 'raw',
						'value' => '$data->first_name',
					),
					array(
						'name' => 'middle_name',
						'type' => 'raw',
						'value' => '$data->middle_name',
					)
				),
			));
		?>
	</div>
</div>