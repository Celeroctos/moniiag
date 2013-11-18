<?php
class IndexController extends Controller {
    public $layout = 'application.modules.doctors.views.layouts.index';
    public $defaultAction = 'index';

    public function actionView() {
        $this->render('index', array());
    }
}

?>