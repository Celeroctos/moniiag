<?php

class LMedcardEditor extends LWidget {

	/**
	 * @var string - Medcard number (mis::medcards::card_number)
	 */
	public $number = null;

	/**
	 * Executes the widget.
	 * This method is called by {@link CBaseController::endWidget}.
	 */
	public function run() {
		if (empty($this->number)) {
			throw new CException("Medcard number can't be empty value");
		}
		$medcard = LMedcard::model()->find("card_number = :card_number", [
			":card_number" => $this->number
		]);
		if ($medcard == null) {
			throw new CException("Medcard with that number doesn't exist");
		}
		$model = new LMedcardForm();
		foreach ($model as $key => $value) {
			$model->$key = $medcard->$key;
		}
		$this->render(__CLASS__, [
			"model" => $model
		]);
	}
}