<?php
/**
 * @var LMedcardEditor $this - Self instance
 * @var LModel $model - Medcard model
 */

$this->widget("LForm", [
	"url" => Yii::app()->getBaseUrl() . "/laboratory/medcard/update",
	"model" => $model
]);