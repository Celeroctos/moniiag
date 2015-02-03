<?php

class GuideController extends LController {

	public function actionView() {
		$this->render("view");
	}

	public function actionRegister() {
		try {
			print json_encode([
				"model" => $this->getFormModel(),
				"status" => true
			]);
		} catch (Exception $e) {
			$this->exception($e);
		}
	}
}