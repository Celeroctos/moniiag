<?php
class SheduleController extends Controller {
    public $layout = 'application.views.layouts.index';
    public $defaultAction = 'view';

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
        $notBeginned = $_GET['notBeginned'];
        $notBeginnedFlag = false;
        if ($notBeginned == 1)
        {
            $notBeginnedFlag = true;
        }


        $model = new SheduleByDay();

        $greetings = $model->getGreetingsPerQrit($filters, false, false, false, $notBeginnedFlag );
        $num = count($greetings);

        $totalPages = ceil($num / $rows);
        $start = $page * $rows - $rows;

        $greetings = $model->getGreetingsPerQrit($filters, $start, $rows,false,$notBeginnedFlag );
        $greetingsAnswer = array();
        foreach($greetings as &$greeting) {
            if($greeting['order_number'] != null && isset($_GET['isCallcenter']) && $_GET['isCallcenter']) {
                continue;
            }
            if($greeting['contact'] == null) {
                if($greeting['phone'] == null) {
                    $greeting['phone'] = '';
                }
            } else {
                $greeting['phone'] = $greeting['contact'];
            }
            $greetingsAnswer[] = $greeting;
        }

        echo CJSON::encode(
            array('rows' => $greetingsAnswer,
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

           /* if($filter['field'] == 'patient_day' && trim($filter['data']) == '') {
                $filter['data'] = date('Y-m-j');
                $filter['op'] = 'ge';
                continue;
            } */

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

	    return $filters;
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

        if(isset($_GET['status'])) {
            $mediateOnly = $_GET['status'];
        } else {
            $mediateOnly = 0;
        }


        if($_GET['forDoctors'] == 1 && $_GET['forPatients'] == 1) {
            $dataD = CJSON::decode($_GET['doctors']);
            $dataP = CJSON::decode($_GET['patients']);
            $dataM = CJSON::decode($_GET['mediates']);
            $filters = array(
                'groupOp' => 'AND',
                'rules' => array(
                    array(
                        'field' => 'doctors_ids',
                        'op' => 'in',
                        'data' => $dataD
                    ),
                    array(
                        'field' => 'patients_ids',
                        'op' => 'in',
                        'data' => $dataP
                    ),
                    array(
                        'field' => 'mediates_ids',
                        'op' => 'in',
                        'data' => $dataM
                    ),
                    array(
                        'field' => 'patient_day',
                        'op' => 'eq',
                        'data' => $_GET['date']
                    )
                )
            );

            $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit($filters, false, false, $mediateOnly);
        } elseif($_GET['forDoctors'] == 1 && $_GET['forPatients'] == 0) {
            $data = CJSON::decode($_GET['doctors']);
            $filters = array(
                'groupOp' => 'AND',
                'rules' => array(
                    array(
                        'field' => 'doctors_ids',
                        'op' => 'in',
                        'data' => $data
                    ),
                    array(
                        'field' => 'patient_day',
                        'op' => 'eq',
                        'data' => $_GET['date']
                    )
                )
            );

            $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit($filters, false, false, $mediateOnly);
        } elseif($_GET['forDoctors'] == 0 && $_GET['forPatients'] == 1) {
            $data = CJSON::decode($_GET['patients']);
            $dataM = CJSON::decode($_GET['mediates']);
            $filters = array(
                'groupOp' => 'AND',
                'rules' => array(
                    array(
                        'field' => 'patients_ids',
                        'op' => 'in',
                        'data' => $data
                    ),
                    array(
                        'field' => 'mediates_ids',
                        'op' => 'in',
                        'data' => $dataM
                    ),
                    array(
                        'field' => 'patient_day',
                        'op' => 'eq',
                        'data' => $_GET['date']
                    )
                )
            );
            $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit($filters, false, false, $mediateOnly);
        } else {
            $filters = array(
                'groupOp' => 'AND',
                'rules' => array(
                    array(
                        'field' => 'patient_day',
                        'op' => 'eq',
                        'data' => $_GET['date']
                    )
                )
            );

            $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit($filters, false, false, $mediateOnly);
        }

        $result = array();
        $num = count($sheduleElements);
        if(isset($mediateElements)) {
            foreach($mediateElements as $element) {
               array_push($sheduleElements, $element);
            }
        }

        $num = count($sheduleElements);
        // Разбираем расписание на живую очередь и обычное раписание
        $sheduleElementsWaitingLine = array();
        $sheduleElementsWriting = array();
        foreach($sheduleElements as $key => $element) {
            if($element['order_number'] != null) {
                $sheduleElementsWaitingLine[] = $element;
            } else {
                $sheduleElementsWriting[] = $element;
            }
        }
        if($num > 0) {
            // Первая сортировка идёт всегда по врачу
            $sheduleElementsWaitingLine = SheduleByDay::sortSheduleElements($sheduleElementsWaitingLine);
            $sheduleElementsWriting = SheduleByDay::sortSheduleElements($sheduleElementsWriting);

            // Вторая сортировка - по времени
            $sheduleElementsWaitingLine = SheduleByDay::makeClusters($sheduleElementsWaitingLine);
            $sheduleElementsWriting = SheduleByDay::makeClusters($sheduleElementsWriting);
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
                    $cabinets[$element['doctor_id']] = null;
                }
            }
        }

        echo CJSON::encode(array('success' => true,
                                 'data' => array('shedule' => $sheduleElements,
                                                 'cabinets' => $cabinets,
                                                 'sheduleOnlyByWriting' => $sheduleElementsWriting,
                                                 'sheduleOnlyWaitingLine' => $sheduleElementsWaitingLine)));
    }
}
?>