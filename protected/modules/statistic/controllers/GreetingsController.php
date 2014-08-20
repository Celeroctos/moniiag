<?php
class GreetingsController extends Controller {
	public $layout = 'application.modules.statistic.views.layouts.index';
    public function actionView() {
		// Список отделений
		$wardsListDb = Ward::model()->getRows(false, 'name', 'asc');

		$wardsList = array('-1' => 'Все');
		foreach($wardsListDb as $value) {
			$wardsList[(string)$value['id']] = $value['name'].', '.$value['enterprise_name'];
		}
		
		// Список специализаций
		$medpersonalListDb = Medworker::model()->getRows(false, 'name', 'asc');

		$medpersonalList = array('-1' => 'Все');
		foreach($medpersonalListDb as $value) {
			$medpersonalList[(string)$value['id']] = $value['name'];
		}
		
		// Список врачей
		$doctorsListDb = Doctor::model()->getRows(false, 'last_name, first_name', 'asc');

		$doctorsList = array('-1' => 'Все');
		foreach($doctorsListDb as $value) {
			if($value['last_name'] == null) {
				$value['middle_name'] = '';
			}
			if($value['tabel_number'] == null) {
				$value['tabel_number'] = 'отсутствует';
			}

			$doctorsList[(string)$value['id']] = $value['last_name'].' '.$value['first_name'].' '.$value['middle_name'].', '.$value['post'].', '.$value['ward'].', табельный номер '.$value['tabel_number'];
		}

        $this->render('index', array(
			'modelFilter' => new FormGreetingsFilter(),
			'wardsList' => $wardsList,
			'medpersonalList' => $medpersonalList,
			'doctorsList' => $doctorsList
		));
    }
	
	public function actionGetStat() {
		$filters = $this->checkFilters();
		$stat = Doctor::model()->getDoctorStat($filters);
		 echo CJSON::encode(
			array('success' => true,
				  'data' => $stat
			)
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

        foreach($filters['rules'] as $key => &$filter) {
            if(isset($filter['data'])) {
				if(!is_array($filter['data']) && trim($filter['data']) != '') {
					$allEmpty = false;
				}
				if(is_array($filter['data']) && count($filter['data']) > 0) {
					$allEmpty = false;
				}
            }
			
			if($filter['field'] == 'ward_id' || $filter['field'] == 'medworker_id' || $filter['field'] == 'doctor_id') {
				foreach($filter['data'] as $val) {
					if($val == -1) {
						unset($filters['rules'][$key]);
						break;
					}
				}
			}
			
			if(($filter['field'] == 'patient_day_from' && trim($filter['data']) == '')
				|| ($filter['field'] == 'patient_day_to' && trim($filter['data']) == '')) {
				unset($filters['rules'][$key]);
			}
        }

        if($allEmpty) {
            echo CJSON::encode(array(
					'success' => false,
                    'data' => 'Задан пустой поисковой запрос.'
				)
            );
            exit();
        }

	    return $filters;
    }
}
?>