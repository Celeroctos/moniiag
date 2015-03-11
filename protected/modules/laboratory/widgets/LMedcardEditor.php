<?php

class LMedcardEditor extends LWidget {

	/**
	 * Executes the widget.
	 * This method is called by {@link CBaseController::endWidget}.
	 */
	public function run() {
		$this->render(__CLASS__, [
			"model" => new LPatientFormOLD(),
			"privileges" => $this->getPrivileges()
		]);
	}

	/**
	 * Get list with patient privileges
	 * @return array - Array with privileges
	 */
	private function getPrivileges() {
		$model = new Privilege();
		$list = [
			-1 => 'Нет'
		];
		$rows = $model->getRows(false);
		foreach($rows as $privilege) {
			$list[$privilege['id']] = $privilege['name'].' (Код '.$privilege['code'].')';
		}
		return $list;
	}
}