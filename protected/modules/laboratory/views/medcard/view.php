<?

/**
 * @var $this LController
 */

$this->widget("LModal", [
	"body" => $this->getWidget("LForm", [
		"model" => new LDirectionForm(),
		"url" => Yii::app()->getBaseUrl() . "/laboratory/laboratory/register"
	]),
	"title" => "Регистрация направления",
	"id" => "direction-register-modal",
	"buttons" => [
		"register" => [
			"class" => "btn btn-primary",
			"type" => "submit",
			"text" => "Добавить"
		]
	]
]);

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
	"body" => "<h1>Hello, LKP</h1>",
	"title" => "Редактирования ЛКП",
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
	<button id="medcard-register-button" class="btn btn-success" data-toggle="modal" data-target="#medcard-register-modal">
		Создать ЛКП
	</button>
	<button id="medcard-edit-button" class="btn btn-default disabled" data-loading-text="Загрузка...">
		Редактировать ЛКП
	</button>
</div>