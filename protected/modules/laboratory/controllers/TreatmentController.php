<?php

class TreatmentController extends LController {

	/**
	 * Default view action
	 */
	public function actionView() {
		$this->render("view");
	}

	/**
	 * Override that method to return controller's model
	 * @return LModel - Controller's model instance
	 */
	public function getModel() {
		return null;
	}
}