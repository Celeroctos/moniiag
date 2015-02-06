<link rel="stylesheet" type="text/css" href="<?= Yii::app()->getBaseUrl()?>/css/laboratory.css" />
<script type="text/javascript" src="<?= Yii::app()->getBaseUrl()?>/js/laboratory/core.js"></script>
<script type="text/javascript" src="<?= Yii::app()->getBaseUrl()?>/js/laboratory/form.js"></script>
<script type="text/javascript" src="<?= Yii::app()->getBaseUrl()?>/js/laboratory/laboratory.js"></script>

<?

/**
 * @var $this LController
 */

$this->widget("LConfirmDelete", [
    "title" => "Удалить?",
    "id" => "confirm-delete-modal"
]);

$this->widget("LModal", [
    "body" => $this->getWidget("LForm", [
        "url" => Yii::app()->getBaseUrl()."/laboratory/guide/register",
        "model" => new LGuideForm(),
        "id" => "guide-register-form"
    ]),
    "title" => "Добавление справочника",
    "id" => "guide-register-modal",
    "buttons" => [
        "register" => [
            "class" => "btn btn-primary",
            "type" => "submit",
            "text" => "Добавить"
        ]
    ]
]);

$this->widget("LModal", [
    "title" => "Редактирование значений",
    "id" => "guide-edit-values-modal",
    "buttons" => [
        "register" => [
            "class" => "btn btn-primary",
            "type" => "submit",
            "text" => "Сохранить"
        ]
    ],
    "class" => "modal-lg"
]);

?>

<div class="col-xs-12">
    <div class="col-xs-4">
        <? $this->beginWidget("LPanel", [ "title" => "Справочники", "id" => "guide-panel" ]); $this->widget("LGuideTable"); ?>
        <hr>
        <button data-toggle="modal" data-target="#guide-register-modal" type="button" class="btn btn-primary btn-sm">
            Добавить справочник
        </button>
        <? $this->endWidget(); ?>
    </div>
    <div class="col-xs-8">
        <div class="panel panel-default" id="guide-edit-panel">
            <div class="panel-heading" style="font-size: 15px">
                <b>Редактирование справочника</b>
            </div>
            <div class="panel-body">
                <div class="panel-content">
                    <h4 style="text-align: center">Не выбран справочник</h4>
                </div>
                <div id="guide-panel-button-group" class="hidden">
                    <button type="submit" id="panel-update" class="btn btn-primary">Сохранить</button>
                    <button type="submit" id="panel-cancel" class="btn btn-default">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
</div>