<?php 
class HospitalizationController extends Controller {
	public $layout = 'application.modules.hospital.views.layouts.index';
	public function actionView() {
		$hGrid = new HospitalizationGrid();
		$dataProvider = new CActiveDataProvider('Patient', array(
			'criteria' => array(
				//'with' => array('id', 'last_name', 'first_name', 'middle_name'),
			),
			'pagination' => array(
				'pageSize' => $hGrid->defaultPageSize,
			),
		));

		$this->render('index', array(
			'dataProvider' => $dataProvider
		));
	}
}

?>