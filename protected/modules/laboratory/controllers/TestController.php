<?php

class TestController extends LController {

	public function actionView() {
        $this->render("test");
	}

    public function actionSquare() {
        return print json_encode([
            "value" => ($_POST["value"] * 2)
        ]);
    }

	public function actionRegister() {
		parent::actionRegister();
	}

	/**
     * Override that method to return controller's model
     * @return LModel - Controller's model instance
     */
    public function getModel() {
        return new LTest();
    }
}