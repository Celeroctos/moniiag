<?php
class AnalysisTypesController extends Controller 
{
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'list';

    public function actionList()
    {
        $model=new AnalysisType;
        $this->render('list', ['model'=>$model]);
    }

    public function actionCreate()
    {
        $model=new AnalysisType('analysistypes.create'); //Сценарий [controller].[action]

        if(isset($_POST['AnalysisType']))
        {
            $model->attributes=Yii::app()->request->getPost('AnalysisType');

            if($model->save())
            {
                Yii::app()->user->setFlash('success', 'Вы успешно создали тип анализа!');
                $this->redirect(['analysistypes/list']);
            }
        }

        $this->render('create', [
            'model'=>$model
        ]);
    }

    public function actionUpdate($id)                           
    {
        $model=AnalysisType::model()->findByPk($id); // Сценарий: [controller].[action]

        if($model===null)
        {
            throw new CHttpException(404, 'Обновляемый объект не найден!');
        }
        elseif(isset($_POST['AnalysisType']))
        {
            $model->scenario='analysistypes.update';
            $model->attributes=Yii::app()->request->getPost('AnalysisType');

            if($model->save())
            {
                Yii::app()->user->setFlash('success', 'Вы успешно тип анализа с #ID ' . $model->id . '!');
                $this->redirect(['analysistypes/list']);
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
        $model=AnalysisType::model()->findByPk($id); //Сценарий [controller].[action]
		$this->render('view',array(
			'model'=>$model,
		));
	}

/*
    public function actionEdit() {
        $model = new AnalysisType();
        if(isset($_POST['AnalysisType'])) {
            $model->attributes = $_POST['AnalysisType'];
            if($model->validate()) {
                $analysistype = AnalysisType::model()->findByPk($model->id);

                $this->addEditModel($analysistype, $model, 'Новое отделение успешно добавлено.');
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
        $record=AnalysisType::model()->findByPk($id);

        if($record===null)
        {
            throw new CHttpException(404, 'Удаляемый объект не найден');
        }
        elseif($record->delete())
        {
            Yii::app()->user->setFlash('success', 'Успешное удаление!');
            $this->redirect(['analysistypes/list']);
        }
        //        try {
        //            $analysistype = AnalysisType::model()->findByPk($id);
        //            $analysistype->delete();
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
        $model = new AnalysisType();
        if(isset($_POST['AnalysisType'])) {
            $model->attributes = $_POST['AnalysisType'];
            if($model->validate()) {
                $analysistype = new AnalysisType();
                $this->addEditModel($analysistype, $model, 'Новое отделение успешно добавлено.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
            $this->redirect(['analysistypes/view']);
      }
        $this->render('add', [
            'model'=>$model
        ]);
    }

    private function addEditModel($analysistype, $model, $msg) {
        $analysistype->enterprise_id = $model->enterprise;
        $analysistype->name = $model->name;
        $analysistype->rule_id = $model->ruleId;


        if($analysistype->save()) {
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

            $model = new AnalysisType();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $analysistypes = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(
                array('rows' => $analysistypes,
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
        $model = new AnalysisType();
        $analysistype = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
            'data' => $analysistype)
        );
    }

}

?>