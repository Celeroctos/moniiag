<?php
/**
 * @var LMedcardSearch $this - Self instance
 */
?>

<div class="col-xs-12" onkeydown="if (arguments[0].keyCode == 13) { MedcardSearch.search(); }">
    <div class="col-xs-6">
        <h4>Поиск</h4>
        <? $this->widget("LForm", [ "model" => new LMedcardSearchForm(), "id" => "medcard-search-form" ]); ?>
    </div>
    <div class="col-xs-6">
        <h4>Дата проведения анализа</h4>
        <? $this->widget("LForm", [ "model" => new LSearchRangeForm(), "id" => "medcard-range-form" ]); ?>
    </div>
</div>
<button id="medcard-search-button" class="btn btn-success" type="button">Поиск</button>
<hr>
<? $this->widget("LMedcardTable") ?>