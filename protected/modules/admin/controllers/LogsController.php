<?php
class LogsController extends Controller {
    public $layout = 'application.views.layouts.index';
    public function actionView() {
        $this->render('view', array());
    }

    public function actionGet() {

    }
}

?>