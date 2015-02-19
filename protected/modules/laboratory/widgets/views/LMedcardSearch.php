<?php
/**
 * @var LMedcardSearch $this - Self instance
 */
?>

<div class="row">
	<div class="col-xs-12" onkeydown="if (arguments[0].keyCode == 13) { MedcardSearch.search(); }">
		<div class="col-xs-5">
			<h4>Поиск</h4>
			<br>
			<? $this->widget("LForm", [ "model" => new LMedcardSearchForm(), "id" => "medcard-search-form" ]); ?>
		</div>
		<div class="col-xs-5">
			<h4>Дата проведения анализа</h4>
			<br>
			<? $this->widget("LForm", [ "model" => new LSearchRangeForm(), "id" => "medcard-range-form" ]); ?>
		</div>
		<div class="col-xs-2"></div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<hr>
		<button id="search-medcard-button" class="btn btn-success btn-block" type="button">Поиск</button>
		<hr>
		<div id="medcard-search-table-wrapper">
			<? $this->widget("LMedcardTable") ?>
		</div>
	</div>
</div>
