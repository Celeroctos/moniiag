<?php

class LMedcardEditableViewer extends LWidget {

	/**
	 * @var string - Number of medcard to load, if number null, then it will
	 *	load empty editable form with generated new medcard number
	 */
	public $number = null;

	/**
	 * Executes the widget. This method is called
	 * by {@link CBaseController::endWidget}.
	 */
	public function run() {
		if ($this->number == null) {
			$generator = new CardNumberGenerator();
			// TODO - "Fix it by adding new medcard rule for laboratory"
			$number = $generator->generateNumber(20);
		} else {
			$number = $this->number;
		}
		if ($this->number != null) {
			if (!($model = LMedcard::model()->fetchInformation($this->number))) {
				throw new CException("Unresolved medcard number \"{$this->number}\"");
			}
		} else {
			$model = null;
		}
		$this->render(__CLASS__, [
			"number" => $number,
			"model" => $model
		]);
	}
}