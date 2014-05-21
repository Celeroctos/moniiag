<?php
class SheduleController extends Controller {
    public $layout = 'application.views.layouts.index';

    public function actionView() {
        $this->render('view', array());
    }
	
	/* Получить список расписаний */
	public function actionSearch() {
		// Проверим наличие фильтров
        $filters = $this->checkFilters();

        $rows = $_GET['rows'];
        $page = $_GET['page'];
        $sidx = $_GET['sidx'];
        $sord = $_GET['sord'];

        $model = new SheduleByDay();
        if(isset($filters['doctor_id'])) {
            $doctorId = $filters['doctor_id'];
        } else {
            $doctorId = array();
        }

        if(isset($filters['oms_id'])) {
            $patientId = $filters['oms_id'];
        } else {
            $patientId = array();
        }

        if(isset($filters['patient_day'])) {
            $date = $filters['patient_day'];
        } else {
            $date = false;
        }

        if(isset($filters['card_number'])) {
            $cardNumber = $filters['card_number'];
        } else {
            $cardNumber = false;
        }

        if(isset($filters['phone'])) {
            $phone = $filters['phone'];
        } else {
            $phone = false;
        }

        $greetings = $model->getGreetingsPerQrit($patientId, $doctorId, $date, 0, $cardNumber, $phone);
        $num = count($greetings);

        $totalPages = ceil($num / $rows);
        $start = $page * $rows - $rows;

        $greetings = $model->getGreetingsPerQrit($patientId, $doctorId, $date, 0, $cardNumber, $phone, $start, $rows);

        foreach($greetings as &$greeting) {
            if($greeting['contact'] == null) {
                if($greeting['phone'] == null) {
                    $greeting['phone'] = '';
                }
            } else {
                $greeting['phone'] = $greeting['contact'];
            }
        }

        echo CJSON::encode(
            array('rows' => $greetings,
                'total' => $totalPages,
                'records' => $num,
                'success' => true)
        );
	}
	
	public function checkFilters($filters = false) {
		if((!isset($_GET['filters']) || trim($_GET['filters']) == '') && (bool) $filters === false) {
            echo CJSON::encode(array('success' => false,
                                     'data' => 'Задан пустой поисковой запрос.')
            );
            exit();
        }

        $filters = CJSON::decode(isset($_GET['filters']) ? $_GET['filters'] : $filters);
        $allEmpty = true;

        $resultFilters = array();
        foreach($filters['rules'] as &$filter) {
			if(($filter['field'] == 'doctor_id' || $filter['field'] == 'oms_id') && count($filter['data']) == 0) {
				unset($filter);
                continue;
			}

			if(!is_array($filter['data']) && trim($filter['data']) == '') {
				unset($filter);
                continue;
			}

            $allEmpty = false;
            $resultFilters[$filter['field']] = $filter['data'];
        }

        if($allEmpty) {
            echo CJSON::encode(array('success' => false,
									 'data' => 'Задан пустой поисковой запрос.')
            );
            exit();
        }

	    return $resultFilters;
	}

    public function actionGetShedule() {
        if((!isset($_GET['doctors']) && !isset($_GET['patients'])) || !isset($_GET['date']) || trim($_GET['date']) == '') {
            echo CJSON::encode(array('success' => false,
                                     'errors' => array(
                                         'query' => array('Не хватает данных для запроса! Проверьте введённую дату (обязательно) и одно из двух необязательных полей: врач или пациент')
                                     )));
            exit();
        }

        $sheduleElements = array();
        $mediateElements = array();

        if($_GET['forDoctors'] == 1 && $_GET['forPatients'] == 1) {
            $data = CJSON::decode($_GET['patients']);
            if(count($data) > 0) {
                $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit($data, array(), $_GET['date']);
            } else {
                $data = CJSON::decode($_GET['doctors']);
                $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit(array(), $data, $_GET['date']);
                $mediateElements = MediatePatient::model()->getGreetingsPerQrit(array(), $data, $_GET['date']);
            }
        } elseif($_GET['forDoctors'] == 1 && $_GET['forPatients'] == 0) {
            $data = CJSON::decode($_GET['doctors']);
            if(!$_GET['status']) { // Не отмечен флаг "только опосредованнные"
                $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit(array(), $data, $_GET['date']);
            }
            $mediateElements = MediatePatient::model()->getGreetingsPerQrit(array(), $data, $_GET['date']);
        } elseif($_GET['forDoctors'] == 0 && $_GET['forPatients'] == 1) {
            $data = CJSON::decode($_GET['patients']);
            $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit($data, array(), $_GET['date']);
        } else {
            if(!$_GET['status']) { // Не отмечен флаг "только опосредованнные"
                $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit(array(), array(), $_GET['date']);
            }
            $mediateElements = MediatePatient::model()->getGreetingsPerQrit(array(), array(), $_GET['date']);
        }

        $result = array();
        $num = count($sheduleElements);
        foreach($mediateElements as $element) {
           array_push($sheduleElements, $element);
        }

        $num = count($sheduleElements);
        if($num > 0) {
            // Первая сортировка идёт всегда по врачу
            usort($sheduleElements, function($element1, $element2) {
                if($element1['doctor_id'] == $element2['doctor_id']) {
                    return 0;
                } elseif($element1['doctor_id'] < $element2['doctor_id']) {
                    return -1;
                } else {
                    return 1;
                }
            });

            // Делим на кластеры. Каждый кластер сортируем по времени (по дефолту) или по заданному полю
            // Сортируем по времени
            if(count($sheduleElements) > 1) {
                $sheduleElementsSorted = array();
                $currentDoctorId = $sheduleElements[0]['doctor_id'];
                $cluster = array($sheduleElements[0]);
                for($i = 1; $i < $num; $i++) {
                    if($sheduleElements[$i]['doctor_id'] == $currentDoctorId && $i < $num - 1) {
                        $cluster[] = $sheduleElements[$i];
                    } else {
                        if($i == $num - 1) {
                            array_push($cluster, $sheduleElements[$i]);
                        }
                        usort($cluster, function($element1, $element2) {
                            $time1 = strtotime($_GET['date'].' '.$element1['patient_time']);
                            $time2 = strtotime($_GET['date'].' '.$element2['patient_time']);
                            if($time1 < $time2) {
                                return -1;
                            } elseif($time1 > $time2) {
                                return 1;
                            } else {
                                return 0;
                            }
                        });

                        foreach($cluster as $element) {
                            array_push($sheduleElementsSorted, $element);
                        }

                        $cluster = array($sheduleElements[$i]);
                        $currentDoctorId = $sheduleElements[$i]['doctor_id'];
                    }
                }
                $sheduleElements = $sheduleElementsSorted;
            }
        }
        // Теперь выясняем кабинет для каждого пациента. Для этого смотрим дату, смотрим расписание врача
        $cabinets = array();
        foreach($sheduleElements as $element) {
            if(!isset($cabinets[$element['doctor_id']])) {
                $weekday = date('w', strtotime($_GET['date']));
                $cabinetElement = SheduleSetted::model()->getCabinetPerWeekday($weekday, $element['doctor_id']);
                if($cabinetElement != null) {
                    $cabinets[$element['doctor_id']] = array('cabNumber' => $cabinetElement['cab_number'],
                                                             'description' => $cabinetElement['description']);
                } else {
                    $cabinets[$cabinetElement->doctor_id] = null;
                }
            }
        }

        echo CJSON::encode(array('success' => true,
                                 'data' => array('shedule' => $sheduleElements,
                                                 'cabinets' => $cabinets)));
    }
}
?>