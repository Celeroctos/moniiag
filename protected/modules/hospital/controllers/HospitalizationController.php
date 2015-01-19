<?php 
class HospitalizationController extends Controller {
	public $layout = 'application.modules.hospital.views.layouts.index';
	public function actionView() {
		$this->render('index', array());
	}
}

?>