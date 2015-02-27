<?php

class LMedcardViewer extends LWidget {

	/**
	 * @var string - Medcard number
	 * @db mis.medcards.card_number
	 */
	public $number;

	public function run() {

		if (empty($this->number)) {
			throw new CException("Medcard viewer requires medcard number, see LMedcardViewer::number");
		}

		if (($model = LMedcard::model()->fetchByNumber($this->number)) == null) {
			throw new CException("Unresolved medcard number \"{$this->number}\"");
		}

		$model["age"] = DateTime::createFromFormat("Y-m-d", $model["birthday"])
			->diff(new DateTime())->y;

		foreach ($model as $key => &$value) {
			if (empty($value)) {
				$value = "Нет";
			}
		}

		$this->render(__CLASS__, [
			"model" => $model
		]);
	}
}