<?php 
class GridController extends Controller {
	public $layout = 'application.modules.hospital.views.layouts.index';
	public $defaultAction = 'index';

	public function actionIndex() {
        if(!isset($_GET['serverModel'])) {
            echo CJSON::encode(array(
                'success' => false,
                'data' => 'Not found server model parameter'
            ));
            exit();
        }

        if(!isset($_GET['perPage'])) {
            $perPage = 10;
        } else {
            $perPage = $_GET['perPage'];
        }

        $model = new $_GET['serverModel']();

//var_dump($model->attributes);
  //      exit();
		$dataProvider = new CActiveDataProvider($_GET['serverModel'], array(
			'criteria' => array(
				//'with' => array('id', 'last_name', 'first_name', 'middle_name'),
			),
			'pagination' => array(
				'pageSize' => $perPage,
				'route' => 'grid/index'
			),
			'sort' => array(
				'route' => 'grid/index'
			)
		));

		$answerData =  array(
			'dataProvider' => $dataProvider,
			'model' => $model,
			'gridId' => $_GET['id'],
            'columns' => CJSON::decode($_GET['model'])
		);
		if(isset($_GET['returnAsJson'])) {
			unset($_GET['returnAsJson']); // This fix is for CGridView, it renders only first time through JSON

			$rendered = $this->renderPartial('view', $answerData, true, true);

			echo CJSON::encode(array(
				'success' => true,
				'data' => $rendered
			));
		} else {
			$this->renderPartial('view', $answerData, false, true);
		}
	}
}

?>