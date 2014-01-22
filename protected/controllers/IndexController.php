<?php
class IndexController extends Controller {
    public $layout = 'index';

    public function actionIndex() {
        $this->render('index', array());
    }

    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
            $this->render('error', $error);
    }
}

?>