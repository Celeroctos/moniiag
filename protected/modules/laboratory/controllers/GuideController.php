<?php

class GuideController extends LController {

	public function actionRegister() {
		try {
			print json_encode([
				"status" => true
			]);
		} catch (Exception $e) {
			$this->exception($e);
		}
	}
}