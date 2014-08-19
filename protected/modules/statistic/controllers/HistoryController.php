<?php
class HistoryController extends Controller {
	public $layout = 'application.modules.statistic.views.layouts.index';
    public function actionView() {
        $this->render('index', array());
    }
}
?>