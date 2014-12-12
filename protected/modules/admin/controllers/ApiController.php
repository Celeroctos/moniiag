<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-12-10
 * Time: 11:28
 */

class ApiController extends Controller {

    public function actionView() {
        $this->render('view', array());
    }

	public function actionRule() {
		$this->render('rule', array());
	}

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

            $model = new Api();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $order = array(
                'key' => 'key',
                'description' => 'description'
            );

            if(isset($order[$sidx])) {
                $sidx = $order[$sidx];
            }

            $items = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(array(
                'rows' => $items,
                'total' => $totalPages,
                'records' => count($num),
                'success' => 'true'
            ));
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionOne($key) {
        $row = Api::model()->findByKey($key);
        if ($row != null) {
            print json_encode(array(
                "key" => $row["key"],
                "description" => $row["description"],
                "success" => true
            ));
        } else {
            print json_encode(array(
                "message" => "Unresolved API key",
                "success" => false
            ));
        }
    }

    public function actionAdd() {
        $model = new FormApiAdd();
        if(!isset($_POST['FormApiAdd'])) {
            print json_encode(array(
                "message" => "POST.FormApiAdd",
                "success" => false
            )); die;
        }
        $model->attributes = $_POST['FormApiAdd'];
        if($model->validate()) {
            print json_encode(array(
                "key" => Api::model()->add($model->description),
                "success" => true
            ));
        } else {
            echo CJSON::encode(array(
                'success' => 'false',
                'errors' => $model->errors
            ));
        }
    }

    public function actionEdit() {
        $model = new FormApiEdit();
        if(!isset($_POST['FormApiEdit'])) {
            print json_encode(array(
                "message" => "POST.FormApiEdit",
                "success" => false
            )); die;
        }
        $model->attributes = $_POST['FormApiEdit'];
        if($model->validate()) {
            Api::model()->update($model->key, $model->description);
            print json_encode(array(
                "success" => true
            ));
        } else {
            echo CJSON::encode(array(
                'success' => 'false',
                'errors' => $model->errors
            ));
        }
    }

    public function actionDelete($key) {
        Api::model()->deleteByPk($key);
        print json_encode(array(
            "success" => true
        ));
    }
} 