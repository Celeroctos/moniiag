<?php

/**
 * @var LWidget $this
 */

$this->widget("LForm", [
	"url" => Yii::app()->getBaseUrl()."/laboratory/guide/register",
	"model" => new LGuideForm(),
	"id" => "add-guide-form"
]); ?>
<h4>Столбцы</h4>
<div class="panel panel-default">
	<div class="panel-heading" style="text-align: right">
		<br>
		<div class="column-container">
			<div><? $this->widget("LForm", [ "model" => new LGuideColumnForm() ]) ?><hr></div>
		</div>
		<a href="javascript:void(0)" id="guide-append-column">
			<span class="glyphicon glyphicon-plus"></span>
		</a>
	</div>
</div>