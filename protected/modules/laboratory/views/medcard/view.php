<link rel="stylesheet" type="text/css" href="<?= Yii::app()->getBaseUrl()?>/css/laboratory.css" />
<script type="text/javascript" src="<?= Yii::app()->getBaseUrl()?>/js/laboratory/core.js"></script>
<script type="text/javascript" src="<?= Yii::app()->getBaseUrl()?>/js/laboratory/form.js"></script>
<script type="text/javascript" src="<?= Yii::app()->getBaseUrl()?>/js/laboratory/laboratory.js"></script>

<?

/**
 * @var $this LController
 */

$this->widget("LModal", [
	"body" => $this->getWidget("LForm", [
		"model" => new LDirectionForm(),
		"url" => Yii::app()->getBaseUrl() . "/laboratory/laboratory/register"
	]),
	"title" => "Создание направления",
	"id" => "add-direction-modal",
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
	"id" => "register-medcard-modal",
	"body" => $this->getWidget("LForm", [
		"model" => new LMedcardForm(),
		"url" => Yii::app()->getBaseUrl() . "/laboratory/medcard/register"
	]),
	"buttons" => [
		"register" => [
			"text" => "Создать",
			"class" => "btn btn-primary",
			"type" => "submit"
		]
	]
]);

//$this->widget("LPanel", [
//	"body" => $this->getWidget("LMedcardSearch"),
//	"title" => "Поиск по ЛКП",
//	"collapse" => "true",
//	"id" => "body-test-collapse"
//]);

$this->widget("LMedcardSearch");

?>
<hr>
<button id="register-medcard-button" class="btn btn-success btn-block" data-toggle="modal" data-target="#register-medcard-modal">Создать ЛКП</button>