<?php
class MonitoringController extends CController {
	public $layout = 'application.modules.hospital.views.layouts.index';
	public function actionView() {
		$this->render('view', array(
			'modelSensorAddEdit' => new FormSensorAddEdit()
		));
	}
}
?>