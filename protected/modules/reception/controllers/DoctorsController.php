<?php
class DoctorsController extends Controller {
    public $layout = 'application.views.layouts.index';

    // Экшн поиска врача
    public function actionSearch() {
        echo CJSON::encode(array('success' => true,
                                 'data' => $this->searchDoctors()
        ));
    }

    // Поиск врачей
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