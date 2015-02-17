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

        $model = new $_GET['serverModel']('grid.view');
        $model->unsetAttributes();

        if(isset($_GET[$_GET['serverModel']])) {
            $model->attributes = Yii::app()->request->getQuery($_GET['serverModel']);
        }
        $grid = new Grid($model->getColumnsModel());
        $model->parentController = $this;

		$answerData =  array(
			'dataProvider' => $model->search(),
			'model' => $model,
			'gridId' => $_GET['id'],
            'serverModel' => $_GET['serverModel'],
            'columns' => $grid->parse()->getColumns(),
            'container' => $_GET['container']
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