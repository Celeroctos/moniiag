<?php
/**
 * @var LMedcardSearch $this - Self instance
 */
?>

<div class="col-xs-12">
    <div class="col-xs-6">
        <h4>Поиск</h4>
        <? $this->widget("LForm", [ "model" => new LMedcardSearchForm() ]); ?>
    </div>
    <div class="col-xs-6">
        <h4>Дата проведения анализа</h4>
        <? $this->widget("LForm", [ "model" => new LSearchRangeForm() ]); ?>
    </div>
</div>
<button class="btn btn-success" type="button">Поиск</button>
<hr>
<? $this->widget("LMedcardTable") ?>