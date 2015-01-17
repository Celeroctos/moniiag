<?php

class TestController extends LController {

	public function actionTest() {
		$this->leave([
			"message" => "Hello, World"
		]);
	}
}