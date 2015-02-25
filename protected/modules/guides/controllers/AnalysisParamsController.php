<?php
class AnalysisParamsController extends Controller 
{
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'list';

    public function actionList()
    {
        $model=new AnalysisParam;
        $this->render('list', ['model'=>$model]);
    }

    public function actionCreate()
    {
        $model=new AnalysisParam('analysisparams.create'); //Сценарий [controller].[action]

        if(isset($_POST['AnalysisParam']))
        {
            $model->attributes=Yii::app()->request->getPost('AnalysisParam');

            if($model->save())
            {
                Yii::app()->user->setFlash('success', 'Вы успешно создали параметр анализа!');
                $this->redirect(['analysisparams/list']);
            }
        }

        $this->render('create', [
            'model'=>$model
        ]);
    }

    public function actionUpdate($id)                           
    {
        $model=AnalysisParam::model()->findByPk($id); // Сценарий: [controller].[action]

        if($model===null)
        {
            throw new CHttpException(404, 'Обновляемый объект не найден!');
        }
        elseif(isset($_POST['AnalysisParam']))
        {
            $model->scenario='analysisparams.update';
            $model->attributes=Yii::app()->request->getPost('AnalysisParam');

            if($model->save())
            {
                Yii::app()->user->setFlash('success', 'Вы успешно изменили параметр анализа с #ID ' . $model->id . '!');
                $this->redirect(['analysisparams/list']);
            }
        }

        $this->render('update', [
            'model'=>$model
        ]);
    }

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
        $model=AnalysisParam::model()->findByPk($id); //Сценарий [controller].[action]
		$this->render('view',array(
			'model'=>$model,
		));
	}


        /*
    public function actionEdit() {
        $model = new AnalysisParam();
        if(isset($_POST['AnalysisParam'])) {
            $model->attributes = $_POST['AnalysisParam'];
            if($model->validate()) {
                $analysisparam = AnalysisParam::model()->findByPk($model->id);

                $this->addEditModel($analysisparam, $model, 'Новое отделение успешно добавлено.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }
        $this->render('add', [
            'model'=>$model
        ]);
    }
*/
    public function actionDelete($id) 
    {
        $record=AnalysisParam::model()->findByPk($id);

        if($record===null)
        {
            throw new CHttpException(404, 'Удаляемый объект не найден');
        }
        elseif($record->delete())
        {
            Yii::app()->user->setFlash('success', 'Успешное удаление!');
            $this->redirect(['analysisparams/list']);
        }
        //        try {
        //            $analysisparam = AnalysisParam::model()->findByPk($id);
        //            $analysisparam->delete();
        //            echo CJSON::encode(array('success' => 'true',
        //                                     'text' => 'Отделение успешно удалено.'));
        //        } catch(Exception $e) {
        //            // Это нарушение целостности FK
        //            echo CJSON::encode(array('success' => 'false',
        //                'error' => 'На данную запись есть ссылки!'));
        //        }
    }
/*
    public function actionAdd() {
        $model = new AnalysisParam();
        if(isset($_POST['AnalysisParam'])) {
            $model->attributes = $_POST['AnalysisParam'];
            if($model->validate()) {
                $analysisparam = new AnalysisParam();
                $this->addEditModel($analysisparam, $model, 'Новое отделение успешно добавлено.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
            $this->redirect(['analysisparams/view']);
      }
        $this->render('add', [
            'model'=>$model
        ]);
    }

    private function addEditModel($analysisparam, $model, $msg) {
        $analysisparam->enterprise_id = $model->enterprise;
        $analysisparam->name = $model->name;
        $analysisparam->rule_id = $model->ruleId;


        if($analysisparam->save()) {
            echo CJSON::encode(array('success' => true,
                'text' => $msg));
        }
    }
  */
    public function actionGet() {
        try {
            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];

            // Фильтры поиска
            if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
                $filters = CJSON::decode($_GET['filters']);
            } else {
                $filters = false;
            }

            $model = new AnalysisParam();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $analysisparams = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(
                array('rows' => $analysisparams,
                    'total' => $totalPages,
                    'records' => count($num),
                    'success' => true
                )
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetone($id) {
        $model = new AnalysisParam();
        $analysisparam = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
            'data' => $analysisparam)
        );
    }

}

?>