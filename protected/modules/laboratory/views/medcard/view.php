<?

/**
 * @var $this LController
 */

$this->widget("LModal", [
	"title" => "Регистрация ЛКП",
	"id" => "medcard-register-modal",
	"body" => $this->getWidget("LForm", [
		"model" => new LMedcardForm(),
		"url" => Yii::app()->getBaseUrl() . "/laboratory/medcard/register"
	]),
	"buttons" => [
		"register" => [
			"text" => "Сохранить",
			"class" => "btn btn-primary",
			"type" => "submit"
		]
	]
]);

$this->widget("LModal", [
	"title" => "Редактирования ЛКП",
    "body" => "",
	"id" => "medcard-edit-modal",
	"buttons" => [
		"register" => [
			"class" => "btn btn-primary",
			"type" => "submit",
			"text" => "Сохранить"
		]
	]
]);

$this->widget("LMedcardSearch");

?>
<hr>
<div class="btn-group" role="group">
	<a id="medcard-register-button" class="btn btn-success" href="<?= Yii::app()->getBaseUrl() . "/reception/patient/viewadd" ?>">
		Создать ЛКП
	</a>
	<button id="medcard-edit-button" class="btn btn-default disabled" data-loading-text="Загрузка...">
		Редактировать ЛКП
	</button>
</div>