<?php
class DoctorsController extends Controller {
    public $layout = 'application.views.layouts.index';

    // Экшн поиска врача
    public function actionSearch() {
        $filters = $this->checkFilters();
        $rows = $_GET['rows'];
	$page = $_GET['page'];
        $sidx = $_GET['sidx'];
        $sord = $_GET['sord'];
        
        /*
         // Вычислим общее количество записей
	$num = $model->getRows($filters,false,false,false,false,$WithOnly,$WithoutOnly);

	$totalPages = ceil(count($num) / $rows);
        $start = $page * $rows - $rows;
	

	
	$omsItems = $model->getRows($filters, $sidx, $sord, $start, $rows,$WithOnly,$WithoutOnly);
        */
        
        
        
        $model = new Doctor();
        
        // Вычислим общее количество записей
	$num = $model->getRows($filters);
        $totalPages = ceil(count($num) / $rows);
        $start = $page * $rows - $rows;
        
        
        $doctors = $model->getRows($filters, $sidx, $sord, $start, $rows);
        
        echo CJSON::encode(array('success' => true,
                                 'data' => $doctors,
                                 'total' => $totalPages,
				 'records' => count($num)));
        
    }
    
    private function checkFilters($filters = false)
    {
                if((!isset($_GET['filters']) || trim($_GET['filters']) == '') && (bool)$filters === false) {
            echo CJSON::encode(array('success' => false,
                                     'data' => 'Задан пустой поисковой запрос.')
            );
            exit();
        }

        $filters = CJSON::decode(isset($_GET['filters']) ? $_GET['filters'] : $filters);
        $allEmpty = true;
        foreach($filters['rules'] as $key => &$filter) {
            if(($filter['field'] == 'ward_code' || $filter['field'] == 'post_id') && $filter['data'] == -1) {
                unset($filters['rules'][$key]);
                continue;
            }
            if(trim($filter['data']) != '') {
                $allEmpty = false;
            }
        }

        if($allEmpty) {
            echo CJSON::encode(array('success' => false,
                                     'data' => 'Задан пустой поисковой запрос.')
            );
            exit();
        }
        
        return $filters;
    }
    
    // Старое - потом убрать
    /*
    public function actionSearch() {
        
         
        
        echo CJSON::encode(array('success' => true,
                                 'data' => $this->searchDoctors()
        ));
    }
    */

    // Поиск врачей
    // НЕ ИСПОЛЬЗУЕТСЯ
    private function searchDoctors($filters = false) {
        if((!isset($_GET['filters']) || trim($_GET['filters']) == '') && (bool)$filters === false) {
            echo CJSON::encode(array('success' => false,
                                     'data' => 'Задан пустой поисковой запрос.')
            );
            exit();
        }

        $filters = CJSON::decode(isset($_GET['filters']) ? $_GET['filters'] : $filters);
        $allEmpty = true;
        foreach($filters['rules'] as $key => &$filter) {
            if(($filter['field'] == 'ward_code' || $filter['field'] == 'post_id') && $filter['data'] == -1) {
                unset($filters['rules'][$key]);
                continue;
            }
            if(trim($filter['data']) != '') {
                $allEmpty = false;
            }
        }

        if($allEmpty) {
            echo CJSON::encode(array('success' => false,
                                     'data' => 'Задан пустой поисковой запрос.')
            );
            exit();
        }

        $model = new Doctor();
        $doctors = $model->getRows($filters);
        return $doctors;
    }
}

?>