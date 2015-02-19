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

$this->widget("LPanel", [
    "body" => $this->getWidget("LMedcardSearch"),
    "title" => "Поиск по ЛКП",
    "collapse" => "true",
    "id" => "body-test-collapse"
]);

?>

<script>
    $(document).ready(function() {
        $("#add-guide-modal").modal();
    });
</script>