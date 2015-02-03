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
        "url" => Yii::app()->getBaseUrl()."/laboratory/guide/register",
        "model" => new LGuideForm()
    ]),
    "title" => "Добавление справочника",
    "id" => "add-guide-modal",
    "buttons" => [
        "register" => [
            "class" => "btn btn-primary",
            "type" => "submit",
            "text" => "Добавить"
        ]
    ]
]);

$this->widget("LConfirmDelete", [
    "title" => "Удалить?",
    "id" => "confirm-delete-modal"
]);

$this->widget("LTable", [
    "table" => new LGuide(),
    "header" => [
        "id" => [
            "label" => "#",
            "style" => "min-width: 0px; width: 10px;"
        ],
        "name" => [
            "label" => "Название"
        ]
    ],
    "id" => "guide-table"
]);

?>

<div class="col-xs-12">
    <div class="col-xs-4">

    </div>
</div>
