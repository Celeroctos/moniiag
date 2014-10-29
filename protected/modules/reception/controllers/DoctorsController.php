<?php
class DoctorsController extends Controller {
    public $layout = 'application.views.layouts.index';
    private $choosedDiagnosis = array();
    private $greetingDate = null;

    // Экшн поиска врача
    public function actionSearch() {
        //var_dump($_POST);
        //exit();
        // Посмотрим на то, какой календарь мы показываем сейчас
        $calendarTypeSetting = Setting::model()->find('name = :name', array(':name' => 'calendarType'))->value;

        $filters = $this->checkFilters();
        if($calendarTypeSetting == 0) {
            $rows = $_GET['rows'];
            $page = $_GET['page'];
        } else {
            $rows = false; // Всё на одной странице
            $page = 1;
        }
        $sidx = $_GET['sidx'];
        $sord = $_GET['sord'];

        $model = new Doctor();

        if(isset($_GET['greeting_id'])) {
            $greetingModel = SheduleByDay::model()->findByPk($_GET['greeting_id']);
            if($greetingModel != null) {
                $patientTimestamp = strtotime($greetingModel->patient_day);
                while(true) {
                    $weekday = date('w', $patientTimestamp);
                    if($weekday == 1) { // Понедельник
                        break;
                    }
                    $patientTimestamp -= 24 * 3600;
                }
                $this->greetingDate = date('Y-n-j', $patientTimestamp);
            }
        }

        if(isset($_GET['beginDate'])) {
            if($calendarTypeSetting == 1) {
                $this->greetingDate = $_GET['beginDate'];
            } else {
                $this->greetingDate = false;
            }
        }

        if(isset($_GET['is_callcenter']) && $_GET['is_callcenter'] == 1) {
            $isCallCenter = true;
        } else {
            $isCallCenter = false;
        }

        // Вычислим общее количество записей
	    $num = $model->getRows($filters, false, false, false, false, $this->choosedDiagnosis, $this->greetingDate, $calendarTypeSetting, $isCallCenter);

        if($calendarTypeSetting == 0) {
            $totalPages = ceil(count($num) / $rows);
        } else {
            $totalPages = 1;
        }
        $start = $page * $rows - $rows;

        //var_dump($filters);
        //exit();
        //$filters['rules'] = array();
		$test = count($num);

        $doctors = $model->getRows($filters, $sidx, $sord, $start, $rows, $this->choosedDiagnosis, $this->greetingDate, $calendarTypeSetting, $isCallCenter);

        // Посмотрим на то, какой календарь мы показываем сейчас
        $calendarTypeSetting = Setting::model()->find('name = :name', array(':name' => 'calendarType'))->value;

        if($calendarTypeSetting == 1) {
            $calendarController = Yii::app()->createController('doctors/shedule');
        }

        // Если дата не задана, считаем, что дата начала показа органайзера - от текущей даты
        if($calendarTypeSetting == 1) {
			if ($this->greetingDate == null || $this->greetingDate == false) {
				$beginYear = date('Y');
				$beginMonth = date('n');
				$beginDay = date('j');
			} else {
				$parts = explode('-', $this->greetingDate);
				$beginYear = $parts[0];
				$beginMonth = $parts[1];
				$beginDay = $parts[2];
			}
		}

        if(isset($_GET['onlywaitingline']) && $_GET['onlywaitingline'] == 1) {
            $onlyWaitingLine = 1;
        } else {
            $onlyWaitingLine = 0;
        }

        // Теперь обработаем врачей: ближайшую свободную дату можно взять из календаря
        foreach($doctors as &$doctor) {
            if($doctor['middle_name'] == null) {
                $doctor['middle_name'] = '';
            }
            $nearFree = $this->getNearFreeDay($doctor['id']);
            $doctor['nearFree'] = $nearFree !== false ? $nearFree : '';
            $doctor['cabinet'] = '';
            if($nearFree) {
                $weekday = date('w', strtotime($nearFree));
                $cabinetElement = SheduleSetted::model()->getCabinetPerWeekday($weekday, $doctor['id']);
                if($cabinetElement != null) {
                    $doctor['cabinet'] = $cabinetElement['cab_number'].' ('.$cabinetElement['description'].')';
                }
            }
            // Если это органайзер, то нам нужно вынимать также часть календаря для каждого врача
            if($calendarTypeSetting == 1) {
                $daysList = $calendarController[0]->getCalendar($doctor['id'], $beginYear, $beginMonth, $beginDay, $breakByErrors = false, $onlyWaitingLine);
                $doctor['shedule'] = $daysList;
            }
        }

        $answer = array(
            'success' => true,
            'data' => $doctors,
            'total' => $totalPages,
            'records' => count($num),
			'test' => $test
        );
        if($calendarTypeSetting == 1) {
            $answer['year'] = $beginYear;
            $answer['month'] = $beginMonth;
            $answer['day'] = $beginDay;

            $restDays = SheduleRest::model()->findAll();
            $restDaysArr = array();
            foreach($restDays as $restDay) {
                $restDaysArr[] = $restDay->day;
            }

            $answer['restDays'] = $restDaysArr;
        }

        // Вынимаем настройки для предельных времён: беременности и первичного приёма
        $primaryGreetingsLimit = Setting::model()->find('module_id = 1 AND name = :name', array(':name' => 'primaryGreetingsLimit'));
        $pregnantGreetingsLimit = Setting::model()->find('module_id = 1 AND name = :name', array(':name' => 'pregnantGreetingsLimit'));
        $callCenterGreetingsLimit = Setting::model()->find('module_id = 1 AND name = :name', array(':name' => 'maxGreetingsInCallcenter'));
        $waitingLineTimeWriting = Setting::model()->find('module_id = 1 AND name = :name', array(':name' => 'waitingLineTimeWriting'));
        $waitingLineDateWriting = Setting::model()->find('module_id = 1 AND name = :name', array(':name' => 'waitingLineDateWriting'));
        if($primaryGreetingsLimit != null) {
            $answer['primaryGreetingsLimit'] = $primaryGreetingsLimit->value;
        } else {
            $answer['primaryGreetingsLimit'] = null;
        }

        if($pregnantGreetingsLimit != null) {
            $answer['pregnantGreetingsLimit'] = $pregnantGreetingsLimit->value;
        } else {
            $answer['pregnantGreetingsLimit'] = null;
        }

        if($callCenterGreetingsLimit != null) {
            $answer['callCenterGreetingsLimit'] = $callCenterGreetingsLimit->value;
        } else {
            $answer['callCenterGreetingsLimit'] = null;
        }

        if($waitingLineTimeWriting != null) {
            $answer['waitingLineTimeWriting'] = $waitingLineTimeWriting->value;
        } else {
            $answer['waitingLineTimeWriting'] = null;
        }
        if($waitingLineDateWriting != null) {
            $answer['waitingLineDateWriting'] = $waitingLineDateWriting->value;
        } else {
            $answer['waitingLineDateWriting'] = null;
        }
        echo CJSON::encode($answer);
    }
    
	    // Экшн поиска врача без расписания (по-хорошему надо перенести это в другой контроллер)
    public function actionSearchCommon() {
        $filters = $this->checkFilters();
        $rows = $_GET['rows'];
	    $page = $_GET['page'];
        $sidx = $_GET['sidx'];
        $sord = $_GET['sord'];

        $model = new Doctor();
		if(isset($filters['rules']['greeting_type']) && $filters['rules']['greeting_type'] == 0) {
			unset($filters['rules']['greeting_type']);
		}

        // Вычислим общее количество записей
	    $num = $model->getRows($filters, false, false, false, false, $this->choosedDiagnosis, $this->greetingDate);
        $totalPages = ceil(count($num) / $rows);
        $start = $page * $rows - $rows;
		
        $doctors = $model->getRows($filters, $sidx, $sord, $start, $rows, $this->choosedDiagnosis, $this->greetingDate);
		
        echo CJSON::encode(array('success' => true,
                                 'data' => $doctors,
                                 'total' => $totalPages,
				                 'records' => count($num)));
        
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
            if(($filter['field'] == 'ward_code' || $filter['field'] == 'post_id') && $filter['data'] == -1) {
                unset($filters['rules'][$key]);
                continue;
            }
            if($filter['field'] == 'diagnosis') {
                if(count($filter['data']) > 0) {
                    $this->choosedDiagnosis = $filter['data'];
                    $allEmpty = false;
                }
                unset($filters['rules'][$key]);
            }
            if($filter['field'] == 'greeting_date' && trim($filter['data']) != '') {
                // Стоит проверить, не выходной ли это день
                // Получим день недели
                $parts = explode('-', $filter['data']);
                $weekday = date('w', mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]));
                $sheduleRestDay = SheduleRest::model()->findAll('day = :day', array(':day' => $weekday));
                $sheduleRestDaysAlone = SheduleRestDay::model()->findAll('date = :date', array(':date' => $filter['data']));
                if(count($sheduleRestDay) > 0 || count($sheduleRestDaysAlone) > 0) {
                    echo CJSON::encode(array('success' => false,
                                             'data' => 'День, по которому производится поиск, выходной! Врачи в этот день не работают!')
                    );
                    exit();
                }
                $this->greetingDate = $filter['data'];
                $allEmpty = false;
                unset($filters['rules'][$key]);
            }
            if(($filter['field'] == 'first_name' || $filter['field'] == 'middle_name' || $filter['field'] == 'last_name') && trim($filter['data']) == '') {
                unset($filters['rules'][$key]);
            }
            if($filter['field'] == 'greeting_type') { // Вторичный приём
                unset($filters['rules'][$key]);
                if($filter['data'] != 2) {
                    $filters['rules'][] = array(
                        'field' => 'greeting_type',
                        'op' => 'in',
                        'data' => array(1, 0)
                    );
                }
            }
            if(!is_array($filter['data']) && trim($filter['data']) != '') {
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

    private function getNearFreeDay($doctorId) {
		$shedule = SheduleSetted::model()->findAll('employee_id = :employee_id', array(':employee_id' => $doctorId));
        $numSheduleElements = count($shedule);
        if($numSheduleElements > 0) {
            $currentYear = date('Y');
            $currentMonth = date('n');
            $currentDay = date('j');
            // Логика следующая: если на текущий день нет возможности записать пациента, то делаем дни + 1. Если нет возможности записать на текущий месяц, делаем месяц + 1. С годом аналогично. И проверяем, пока не найдём, куда записать.
            $stableShedule = false;
            for($i = 0; $i < $numSheduleElements; $i++) {
                if($shedule[$i]->weekday != null) {
                    $stableShedule = true;
                }
            }

            // Если стабильное расписание существует, можно посмотреть ближайшую дату
            if($stableShedule) {
                while(true) {
                    $formatDate =  $currentYear.'-'.$currentMonth.'-'.$currentDay;
                    $weekday = date('w', strtotime($formatDate));
                    for($i = 0; $i < $numSheduleElements; $i++) {
                        if($weekday == $shedule[$i]->weekday) {
                            return $currentDay.'.'.$currentMonth.'.'.$currentYear;
                        }
                        // Дробим дату на части, чтобы проверить дни-исключения
                        if($shedule[$i]->day != null) {
                            $parts = explode('-', $shedule[$i]->day);
                            if((int)$parts[0] == $currentYear && (int)$parts[1] == $currentMonth && (int)$parts[2] == $currentDay) {
                                return $currentDay.'.'.$currentMonth.'.'.$currentYear;
                            }
                        }
                    }

                    // Надбавляем величины, начиная с дня
                    if($currentDay + 1 <  date('t', strtotime($formatDate))) {
                        $currentDay++;
                    } else {
                        $currentMonth++;
                        $currentDay = 1;
                    }

                    if($currentMonth > 12) {
                        $currentMonth = 1;
                        $currentYear++;
                    }
                }
            } else {
                // Расписание, состоящее только из частных дней
                $formatDate =  $currentYear.'-'.$currentMonth.'-'.$currentDay;
                $timestampCurrent = mktime(0, 0, 0, $currentMonth, $currentDay, $currentYear);
                $near = false;
                $returnStr = '';
                for($i = 0; $i < $numSheduleElements; $i++) {
                    $parts = explode('-', $shedule[$i]->day);
                    $assetedTime = mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]);
                    if(($near === false && $assetedTime > $timestampCurrent) || ($assetedTime < $near && $assetedTime > $timestampCurrent)) {
                        $returnStr = $parts[2].'.'.$parts[1].'.'.$parts[0];
                        $near = $assetedTime;
                    }
                }
                return $returnStr;
            }
        } else {
            return false; // Расписание не установлено
        }
    }
}

?>