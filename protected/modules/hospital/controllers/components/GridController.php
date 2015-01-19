<?php 
class GridController extends Controller {
	public $layout = 'application.modules.hospital.modules.components.views.layouts.index';
	public $defaultAction = 'index';
	
	public function actionIndex() {
		$hGrid = new HospitalizationGrid();
		$model = new Patient();
		$model->unsetAttributes();
		if(isset($_GET['Patient'])) {
			$model->attributes = $_GET['Patient'];
		}
		
		$dataProvider = new CActiveDataProvider('Patient', array(
			'criteria' => array(
				//'with' => array('id', 'last_name', 'first_name', 'middle_name'),
			),
			'pagination' => array(
				'pageSize' => $hGrid->defaultPageSize,
				'route' => 'components/grid/index'
			),
			'sort' => array( 
				'route' => 'components/grid/index'
			)
		));
		
		$answerData =  array(
			'dataProvider' => $dataProvider,
			'model' => $model,
			'gridId' => $_GET['id']
		);
		if(isset($_GET['returnAsJson'])) {
			unset($_GET['returnAsJson']); // This fix is for CGridView, it renders only first time through JSON
			
			$rendered = $this->renderPartial('application.modules.hospital.modules.components.views.grid.view', $answerData, true, true);
			
			echo CJSON::encode(array(
				'success' => true,
				'data' => $rendered
			));
		} else {
			$this->renderPartial('application.modules.hospital.modules.components.views.grid.view', $answerData, false, true);
		}
	}
}

?>