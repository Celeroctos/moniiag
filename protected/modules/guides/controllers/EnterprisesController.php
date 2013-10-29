<?php
class EnterprisesController extends Controller {
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
            $enterprises = $connection->createCommand()
                            ->select('ep.*, et.name as enterprise_type')
                            ->from('mis.enterprise_params ep')
                            ->join('mis.enterprise_types et', 'ep.type = et.id')
                            ->queryAll();

            foreach($enterprises as $key => &$enterprise) {
                $enterprise['requisits'] = 'Банк '.$enterprise['bank'].', '.$enterprise['bank_account'].', ИНН '.$enterprise['inn'].', КПП '.$enterprise['kpp'];
            }
            echo CJSON::encode($enterprises);

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>