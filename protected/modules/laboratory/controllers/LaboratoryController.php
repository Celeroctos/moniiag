<?php

class LaboratoryController extends LController {

	public function actionRegister() {
		print json_encode([
			"model" => $this->getFormModel("model", "post"),
			"status" => true
		]);
	}

	/**
	 * Override that method to return controller's model
	 * @return LModel - Controller's model instance
	 */
	public function getModel() {
		return null;
	}
}