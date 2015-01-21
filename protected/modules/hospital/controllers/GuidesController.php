<?php
class GuidesController extends Controller {
    public $layout = 'application.modules.hospital.views.layouts.guides';
    public function actionView() {
        $this->render('view', array());

    }
}

?>