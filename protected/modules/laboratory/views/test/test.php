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
    "body" => $this->widget("LForm", [
        "model" => new LMedcardForm(),
        "id" => "test-form"
    ], true),
    "id" => "test-modal"
]);

?>

<button class="btn btn-primary" data-toggle="modal" data-target="#add-direction-modal">Test</button>

<script>
	var reloadPage = function(page) {
		window.location.href = "/moniiag/laboratory/test/view?page=" + page;
	};
</script>