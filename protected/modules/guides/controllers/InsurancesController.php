<?php
class InsurancesController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        $this->render('view', array());
    }

}
?>