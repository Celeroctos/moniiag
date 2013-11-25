<?php
class IndexController extends Controller {
    public $layout = 'application.modules.settings.views.layouts.index';
    public $formModel = null;

    public function actionView() {
        $this->render('index', array(
        ));
    }
}
?>