<?php

class AnalysisTypeTemplateController extends Controller
{
    public $layout = 'application.modules.guides.views.layouts.index';

	public function actionView($id)
	{
        $this->actionIndex();
	}

    public function actionCreate() {

        $model = new AnalysisTypeTemplate('analysistypetemplates.create');

        if (isset($_POST['AnalysisTypeTemplate'])) {
            $model->attributes = $_POST['AnalysisTypeTemplate'];
            if ($model->save()) {
                if (Yii::app()->request->isAjaxRequest) {
                    echo 'success';
                    Yii::app()->end();
                } else {
                    $this->redirect(array('view', 'id' => $model->id));
                }
            }
        }
        if (Yii::app()->request->isAjaxRequest)
            $this->renderPartial('create', array('model' => $model), false, true);
        else
            $this->render('create', array('model' => $model));
    }

    public function actionUpdate($id) {

        $model = $this->loadModel($id);

        if (isset($_POST['AnalysisTypeTemplate'])) {
            $model->scenario = 'analysistypetemplates.update';
            $model->attributes = $_POST['AnalysisTypeTemplate'];
            if ($model->save()) {
                if (Yii::app()->request->isAjaxRequest) {
                    echo 'success';
                    Yii::app()->end();
                } else {
                    $this->redirect(array('view', 'id' => $model->id));
                }
            }
        }
        if (Yii::app()->request->isAjaxRequest)
            $this->renderPartial('update', array('model' => $model), false, true);
        else
            $this->render('update', array('model' => $model));
    }

    public function actionDelete($id) {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $model=new AnalysisType;
#        $model = new AnalysisTypeTemplate('search');
        $model->unsetAttributes();  // clear any default values
 #       if (isset($_GET['AnalysisTypeTemplate']))
 #           $model->attributes = $_GET['AnalysisTypeTemplate'];

        $this->render('index', array(
            'model' => $model,
        ));
    }


    public function loadModel($id) {
        $model = AnalysisTypeTemplate::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

}