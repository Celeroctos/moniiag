<?php

class LaboratoryController extends LController {

	public function actionRegister() {
		print json_encode([
			"model" => $this->post("model"),
			"status" => true
		]);
	}
}