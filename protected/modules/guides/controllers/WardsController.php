<?php
class WardsController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        $this->render('view', array());
    }

    public function actionEdit() {

    }

    public function actionDelete() {


    }

    public function actionGet() {
        try {
            $connection = Yii::app()->db;
            $wards = $connection->createCommand()
                ->select('mw.*, e.shortname as enterprise_name')
                ->from('mis.wards mw')
                ->join('mis.enterprise_params e', 'mw.enterprise_id = e.id')
                ->queryAll();
            echo CJSON::encode($wards);
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>