<?php
class RolesController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        $this->render('index', array());
    }
}

?>