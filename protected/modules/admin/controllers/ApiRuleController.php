<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-12-10
 * Time: 11:28
 */

class ApiRuleController extends Controller {

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

            $model = new ApiRule();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $order = array(
                'api_key' => 'api_key',
                'description' => 'description',
				'id' => 'id',
				'readable' => 'readable',
				'writable' => 'writable'
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

	public function actionAll($key) {
		$row = ApiRule::model()->findByKey($key);
		if ($row != null) {
			print json_encode(array(
				"rules" => $row,
				"success" => true
			));
		} else {
			print json_encode(array(
				"message" => "Unresolved API key",
				"success" => false
			));
		}
	}

    public function actionOne($id) {
        $row = ApiRule::model()->findByPk($id);
        if ($row != null) {
            print json_encode(array(
                "rule" => $row,
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
        $model = new FormApiRuleAdd();
        if(!isset($_POST['FormApiRuleAdd'])) {
            print json_encode(array(
                "message" => "POST.FormApiRuleAdd",
                "success" => false
            )); die;
        }
        $model->attributes = $_POST['FormApiRuleAdd'];
        if($model->validate()) {
            print json_encode(array(
                "key" => ApiRule::model()->add(
					$model->api_key,
					$model->controller,
					$model->writable,
					$model->readable
				),
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
        $model = new FormApiRuleAdd();
        if(!isset($_POST['FormApiRuleAdd'])) {
            print json_encode(array(
                "message" => "POST.FormApiRuleAdd",
                "success" => false
            )); die;
        }
        $model->attributes = $_POST['FormApiRuleAdd'];
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

    public function actionDelete($id) {
        Api::model()->deleteByPk($id);
        print json_encode(array(
            "success" => true
        ));
    }
} 