<?

/**
 * @var $this LController
 */

$this->widget("LModal", [
    "body" => $this->getWidget("LForm", [
        "model" => new LAnalysisParamForm("update"),
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

print "<pre>";
print_r(array_keys($_GET));
print "</pre>";

$this->widget("LPagination", [
	"pages" => 50,
	"page" => isset($_GET["page"]) ? $_GET["page"] : 1,
	"limit" => 10,
	"action" => "reloadPage.call"
]);


$this->widget("LModal", [
	"body" => $this->getWidget("LForm", [
		"model" => new LTestForm("register"),
		"id" => "test-form",
		"url" => "/moniiag/laboratory/test/register"
	]),
	"title" => "Создание тестовых данных",
	"id" => "test-modal",
	"buttons" => [
		"register" => [
			"class" => "btn btn-primary",
			"type" => "submit",
			"text" => "Сохранить"
		]
	]
]);

?>

<br><br><br><br>
<button class="btn btn-primary" data-toggle="modal" data-target="#test-modal">Test</button>
<script>
	var reloadPage = function(page) {
		window.location.href = "/moniiag/laboratory/test/view?page=" + page;
	};
</script>