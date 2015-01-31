<link rel="stylesheet" type="text/css" href="<?= Yii::app()->getBaseUrl()?>/css/laboratory.css" />
<script type="text/javascript" src="<?= Yii::app()->getBaseUrl()?>/js/laboratory/core.js"></script>
<script type="text/javascript" src="<?= Yii::app()->getBaseUrl()?>/js/laboratory/message.js"></script>
<script type="text/javascript" src="<?= Yii::app()->getBaseUrl()?>/js/laboratory/form.js"></script>

<?

/**
 * @var $this LController
 */

$this->widget("LModal", [
    "body" => $this->getWidget("LForm", [
        "id" => "add-guide-form",
        "url" => Yii::app()->getBaseUrl() . "/laboratory/guide/register",
        "model" => new LGuideValueForm()
    ]),
    "title" => "Добавление справочника",
    "id" => "add-guide-modal"
]);

?>

<script>
    $(document).ready(function() {
        $("#add-guide-modal").modal();
    });
</script>