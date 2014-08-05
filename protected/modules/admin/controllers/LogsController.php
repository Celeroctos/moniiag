<?php
class LogsController extends Controller {
    public $layout = 'application.views.layouts.index';
    public function actionView() {
        $this->render('view', array());
    }

    public function actionSearch() {
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
		
		$filters = $this->checkFilters($filters);

		$model = new Log();
		$num = $model->getNumRows($filters);

		$totalPages = ceil($num['num'] / $rows);
		$start = $page * $rows - $rows;
		
		$logs = $model->getRows($filters, $sidx, $sord, $start, $rows);
		foreach($logs as &$log) {
			$log['changedate'] = implode('.', array_reverse(explode('-', $log['changedate'])));
		}
		
		echo CJSON::encode(
			array('rows' => $logs,
				  'total' => $totalPages,
				  'success' => true,
				  'records' => $num['num'])
		);
    }
	
	 private function checkFilters($filters = false) {
	    if((!isset($_GET['filters']) || trim($_GET['filters']) == '') && (bool)$filters === false) {
            echo CJSON::encode(array('success' => false,
                                     'data' => 'Задан пустой поисковой запрос.')
            );
            exit();
        }

        $filters = CJSON::decode(isset($_GET['filters']) ? $_GET['filters'] : $filters);
        $allEmpty = true;

        foreach($filters['rules'] as &$filter) {
			if(!is_array($filter['data']) && trim($filter['data']) == '') {
				unset($filter);
				continue;
			}
			if(is_array($filter['data']) && count($filter['data']) == 0) {
				unset($filter);
				continue;
			}
		}
		return $filters;
	}

    public function actionDeleteTestCards() {
        $testCards = Medcard::model()->getTestOmsWithCards();
        foreach($testCards as $card) {
            Oms::model()->deleteByPk($card['id']);
            Medcard::model()->deleteByPk($card['card_number']);
        }
    }

}

?>