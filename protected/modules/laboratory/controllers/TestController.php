<?php

class TestController extends LController {

	public function actionView() {
        $this->render("test");
	}

    public function actionGetWidget() {
        parent::actionGetWidget();
    }
}