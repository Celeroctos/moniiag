<?php
class TabmarkController extends CController {
    public $defaultAction = 'get';
    public function actionGet() {
        if(!isset($_GET['serverModel'])) {
            CJSON::encode(array(
                'success' => false,
                'data' => 'Not found serverModel for tabmark'
            ));
            exit();
        }

        $model = $_GET['serverModel']::model();

        echo CJSON::encode(array(
            'success' => true,
            'num' => $model->count()
        ));
    }
}
?>