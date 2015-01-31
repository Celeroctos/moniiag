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

    public function actionGetWidget() {
        parent::actionGetWidget();
    }
}