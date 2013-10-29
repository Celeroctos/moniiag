<?php
class IndexController extends Controller {
    public $layout = 'index';

    public function actionIndex() {
        $this->render('index', array());
    }
}

?>