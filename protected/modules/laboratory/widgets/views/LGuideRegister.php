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
			<? $this->widget("LForm", [ "model" => new LGuideColumnForm() ]) ?><hr>
		</div>
		<a href="javascript:void(0)" id="guide-append-column">
			<span class="glyphicon glyphicon-plus"></span>
		</a>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$("#guide-append-column").click(function() {
			$.get(url("/laboratory/guide/getWidget"), {
				class: "LForm",
				model: "LGuideColumnForm"
			}, function(json) {
				if (!json.status) {
					return Laboratory.createMessage({
						message: json["message"]
					});
				}
				var c = $(json["component"]);
				$(c.find("#guide_id").parents(".form-group")[0]).addClass("hidden");
				$(".column-container").append(c).append("<hr>");
			}, "json");
		});
		$("#guide_id").parents(".form-group").addClass("hidden");
	});
</script>