<?php
class DoctorsController extends Controller {
    public $layout = 'application.views.layouts.index';
    private $choosedDiagnosis = array();
    private $greetingDate = null;

    // Экшн поиска врача
    public function actionSearch() {
        $filters = $this->checkFilters();
        $rows = $_GET['rows'];
	    $page = $_GET['page'];
        $sidx = $_GET['sidx'];
        $sord = $_GET['sord'];

        $model = new Doctor();

	
        // Вычислим общее количество записей
	    $num = $model->getRows($filters, false, false, false, false, $this->choosedDiagnosis, $this->greetingDate);
        $totalPages = ceil(count($num) / $rows);
        $start = $page * $rows - $rows;
		
        $doctors = $model->getRows($filters, $sidx, $sord, $start, $rows, $this->choosedDiagnosis, $this->greetingDate);
	
        // Теперь обработаем врачей: ближайшую свободную дату можно взять из календаря
        foreach($doctors as &$doctor) {
            $nearFree = $this->getNearFreeDay($doctor['id']);
            $doctor['nearFree'] = $nearFree !== false ? $nearFree : '';
            $doctor['cabinet'] = '';
            // На полученную дату выясняем кабинет
            if($nearFree) {
                $weekday = date('w', strtotime($nearFree));
                $cabinetElement = SheduleSetted::model()->getCabinetPerWeekday($weekday, $doctor['id']);
                if($cabinetElement != null) {
                    $doctor['cabinet'] = $cabinetElement['cab_number'].' ('.$cabinetElement['description'].')';
                }
            }
        }

        echo CJSON::encode(array('success' => true,
                                 'data' => $doctors,
                                 'total' => $totalPages,
				                 'records' => count($num)));
        
    }
    
	    // Экшн поиска врача без расписания (по-хорошему надо перенести это в другой контроллер)
    public function actionSearchCommon() {
        $filters = $this->checkFilters();
        $rows = $_GET['rows'];
	    $page = $_GET['page'];
        $sidx = $_GET['sidx'];
        $sord = $_GET['sord'];

        $model = new Doctor();

	
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
                $weekday = date('w', mktime(0, 0, 0, $parts[2], $parts[1], $parts[0]));
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
            return false; // Расписание не установлено
        }
    }
}

?>