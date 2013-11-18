<?php
class SheduleController extends Controller {
    public $layout = 'index';

    public function actionView() {
        $this->render('index', array());
    }
}

?>