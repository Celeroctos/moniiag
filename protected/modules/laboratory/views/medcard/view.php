<script type="text/javascript" src="<?= Yii::app()->getBaseUrl() ?>\js\chooser.js"></script>
<script type="text/javascript" src="<?= Yii::app()->getBaseUrl() ?>\js\reception\searchAddPatient.js"></script>
<?

/**
 * @var $this MedcardController
 */

$this->widget("LModal", [
	"title" => "Редактирование данных медкарты пациента",
	"body" => $this->getWidget("LMedcardEditor"),
	"buttons" => [
		"save" => [
			"text" => "Сохранить",
			"class" => "btn btn-primary",
			"type" => "submit"
		]
	],
	"id" => "patient-medcard-edit-modal"
]);

$this->widget("LMedcardSearch", [
	"mode" => "mis"
]);

?>
<hr>
<div class="btn-group" role="group">
	<a id="medcard-register-button" class="btn btn-success" href="<?= Yii::app()->getBaseUrl() . "/reception/patient/viewadd" ?>">
		Создать ЛКП
	</a>
	<button id="medcard-edit-button" class="btn btn-default disabled" data-loading-text="Загрузка...">
		Редактировать ЛКП
	</button>
    <button id="medcard-show-button" class="btn btn-default disabled" data-loading-text="Загрузка...">
        Открыть ЛКП
    </button>
</div>