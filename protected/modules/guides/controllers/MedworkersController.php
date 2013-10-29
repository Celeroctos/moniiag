<?php
class MedworkersController extends Controller {
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
            $medpersonals = $connection->createCommand()
                ->select('m.*, mt.name as medpersonal_type')
                ->from('mis.medpersonal m')
                ->join('mis.medpersonal_types mt', 'm.type = mt.id')
                ->queryAll();

            echo CJSON::encode($medpersonals);

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>