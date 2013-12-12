<?php
class SheduleController extends Controller {
    public $layout = 'index';
    public $filterModel = null;
    public $currentPatient = false;
    /* Календарь */
    public $currentDay = null;
    public $currentYear = null;
    public $currentMonth = null;

    public function actionView() {
        if(isset($_GET['cardid']) && trim($_GET['cardid']) != '') {
            // Проверим, есть ли такая медкарта вообще
            $medcardFinded = Medcard::model()->findByPk($_GET['cardid']);
            if($medcardFinded != null) {
                $this->currentPatient = trim($_GET['cardid']);
                $medcardModel = new Medcard();
                $medcard = $medcardModel->getOne($this->currentPatient);
                // Вычисляем количество лет
                $parts = explode('-', $medcard['birthday']);
                $medcard['full_years'] = date('Y') - $parts[0];
            }
        }

        $this->filterModel = new FormSheduleFilter();
        $patients = $this->getCurrentPatients();
        $patientsInCalendar = CJSON::encode($this->getDaysWithPatients());
        $curDate = $this->getCurrentDate();

        $parts = explode('-', $curDate);
        $curDate = $parts[2].'.'.$parts[1].'.'.$parts[0];

        $this->render('index', array(
            'patients' => $patients,
            'patientsInCalendar' => $patientsInCalendar,
            'currentPatient' => $this->currentPatient,
            'pregnantContent' => '',
            'filterModel' => $this->filterModel,
            'medcard' => isset($medcard) ? $medcard : null,
            'currentDate' => $curDate,
            'historyPoints' => $this->getHistoryPoints(isset($medcard) ? $medcard : null)
        ));
    }

    // Получить точки истории для медкарты
    private function getHistoryPoints($medcard) {
        if($medcard == null) {
            return array();
        }
        $historyPoints = MedcardElementForPatient::model()->getHistoryPoints($medcard);
        return $historyPoints;
    }

    // Получить даты, в которых у врача есть пациенты
    private function getDaysWithPatients() {
        $shedule = new SheduleByDay();
        return $shedule->getDaysWithPatients(Yii::app()->user->id);
    }

    // Получить текущую дату
    private function getCurrentDate() {
        if(!isset($_POST['FormSheduleFilter']['date']) && !isset($_GET['date'])) {
            $date = date('Y-m-d');
        } else {
            if(isset($_POST['FormSheduleFilter'])) {
                $this->filterModel->attributes = $_POST['FormSheduleFilter'];
            } else {
                $this->filterModel->date = $_GET['date'];
            }

            if($this->filterModel->validate()) {
                $date = $this->filterModel->date;
            } else {
                $date = date('Y-m-d');
            }
        }
        return $date;
    }

    // Редактирование данных пациента
    public function actionPatientEdit() {
        if(isset($_POST['FormTemplateDefault'])) {
            // Перебираем весь входной массив, чтобы записать изменения в базу
            foreach($_POST['FormTemplateDefault'] as $field => $value) {
                if($field == 'medcardId') {
                    continue;
                }
                // Это для выпадающего списка с множественным выбором
                if(is_array($value)) {
                    $value = CJSON::encode($value);
                }
                // Проверим, есть ли такое поле вообще
                if(!preg_match('/^f(\d+)$/', $field, $resArr)) {
                    continue;
                }

                $element = MedcardElement::model()->findByPk($resArr[1]);
                if($element == null) {
                    continue;
                }

                $elementModel = new MedcardElementForPatient();
                $elementModel->medcard_id = $_POST['FormTemplateDefault']['medcardId'];
                $elementModel->element_id = $resArr[1];
                $elementModel->value = $value;
                $elementModel->change_date = date('Y-m-d h:i');
                $historyIdResult = MedcardElementForPatient::model()->getMaxHistoryPointId(array('id' => $elementModel->element_id), $elementModel->medcard_id);
                if($historyIdResult['history_id_max'] == null) {
                    $elementModel->history_id = 1;
                } else {
                    // Дальше смотрим, есть ли уже такой элемент в базе для конкретного пациента. Если есть - будем апдейтить. Если нет - писать. Это позволит не сохранять неизменённые поля
                    $element = MedcardElementForPatient::model()->find('element_id = :element_id
                                                                        AND medcard_id = :medcard_id
                                                                        AND history_id = :history_id',
                                                                array(':medcard_id' => $_POST['FormTemplateDefault']['medcardId'],
                                                                      ':element_id' => $element->id,
                                                                      ':history_id' => $historyIdResult['history_id_max'])
                    );
                    if($element != null) {
                        if(trim($element['value']) == trim($value)) {
                            continue;
                        }
                    }
                    $elementModel->history_id = $historyIdResult['history_id_max'] + 1;
                }
                if(!$elementModel->save()) {
                    echo CJSON::encode(array('success' => true,
                        'text' => 'Ошибка сохранения записи.'));
                    exit();
                }
            }
            echo CJSON::encode(array('success' => true,
                                     'text' => 'Данные успешно сохранены.'));
        } else {
            echo CJSON::encode(array('success' => false,
                                     'text' => 'Ошибка запроса.'));
        }
    }

    // Получить пациентов для текущего дня расписания
    public function getCurrentPatients() {
        $date = $this->getCurrentDate();
        $this->filterModel->date = $date;
        $userId = Yii::app()->user->id;
        $doctor = User::model()->findByPk($userId);
        if($doctor == null) {
            exit('Error!');
        }
        // Выбираем пациентов на обозначенный день
        $sheduleByDay = new SheduleByDay();
        $patients = $sheduleByDay->getRows($date, $doctor['employee_id']);
        return $patients;
    }

    // Закончить приём пациента
    public function actionAcceptComplete() {
        $req = new CHttpRequest();
        if(isset($_GET['id']) && trim($_GET['id']) != '') {
            // Записать, что пациент принят
            $sheduleElement = SheduleByDay::model()->findByPk($_GET['id']);
            if($sheduleElement != null) {
                $sheduleElement->is_accepted = 1;
                if(!$sheduleElement->save()) {
                    echo CJSON::encode(array('success' => true,
                                             'text' => 'Ошибка сохранения записи.'));
                }
            }
        }

        $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/doctors/shedule/view'));
    }

    // Выдать календарь для записи врача
    // С уклоном на виджет
    public function actionGetCalendar() {
        echo CJSON::encode(array('success' => 'true',
                                 'data' => array(
                                     'calendar' => $this->getCalendar(),
                                     'day' => $this->currentDay,
                                     'month' => $this->currentMonth,
                                     'year' => $this->currentYear,
                                     'doctorId' => (isset($_GET['doctorid']) && (int)$_GET['doctorid'] != 0) ? (int)$_GET['doctorid'] : false
                                 )
                        )
        );
    }

    private function getSettings() {
        $settings = Setting::model()->findAll('module_id = 1
                                                    AND name IN(\'timePerPatient\',
                                                                \'firstVisit\',
                                                                \'quote\',
                                                                \'shiftType\')');
        $result = array();
        foreach($settings as $setting) {
            $result[$setting['name']] = $setting['value'];
        }
        return $result;
    }

    // Логика выдачи календаря:
    /* Выдаются даты + характеристика дат. Например, количество пациентов на день. */
    private function getCalendar() {
        // Выбираем расписание врача
        if(isset($_GET['doctorid']) && (int)$_GET['doctorid'] != 0) {
            $doctorId = (int)$_GET['doctorid'];
            // Выбираем настройки расписания
            $settings = $this->getSettings();
            $shedule = SheduleSetted::model()->findAll('employee_id = :employee_id', array(':employee_id' => $doctorId));
            // Здесь проверяем день, месяц, год..
            if(isset($_GET['year'])) {
                $this->currentYear = $_GET['year'];
            } else {
                $this->currentYear = date('Y');
            }
            if(isset($_GET['month'])) {
                $this->currentMonth = $_GET['month'];
            } else {
                $this->currentMonth = date('n');
            }
            if(isset($_GET['day'])) {
                $this->currentDay = $_GET['day'];
            } else {
                $this->currentDay = date('j');
            }
            // Расписание не установлено
            if(count($shedule) == 0) {
                echo CJSON::encode(array('success' => 'false',
                                         'data' => 'Запись невозможна: расписание для данного сотрудника не установлено.'));
                exit();
            }
            // Количество дней в месяце
            $dayBegin = 1;
            if($this->currentYear != null && $this->currentMonth != null) {
                $dayEnd = date('t', strtotime($this->currentYear.'-'.$this->currentMonth));
            } else {
                $dayEnd = date('t');
            }
            // Здесь составляем карту расписания на каждый день: разбираем на общее расписание и исключения
            $usual = array();
            $exps = array();
            foreach($shedule as $key => $element) {
                // Обычное расписание
                if($element['type'] == 0) {
                    array_push($usual, $element['weekday']);
                }
                // Исключения
                if($element['type'] == 1) {
                    array_push($exps, $element['day']);
                }
            }
            // Теперь смотрим по дням и составляем календарь
            $resultArr = array();
            for($i = $dayBegin; $i <= $dayEnd; $i++) {
                $resultArr[$i - 1] = array();

                // Ведущие нули
                $month = $this->currentMonth < 10 ? '0'.$this->currentMonth : $this->currentMonth;
                $day = $i < 10 ? '0'.$i : $i;

                $formatDate =  $this->currentYear.'-'.$month.'-'.$day;
                $weekday = date('w', strtotime($formatDate));
                // 0 -> 0.. 1 -> 1..
                $resultArr[$i - 1]['weekday'] = $weekday;
                if(array_search($weekday, $usual) !== false || array_search($formatDate, $exps) !== false) {
                    // День существует, врач работает
                    $resultArr[$i - 1]['worked'] = true;
                    // Дальше, исходя из настроек, смотрим: полностью свободный, частично свободный или полностью занятый день
                    // TODO: в цикле очень плохо делать выборку. 31 выборка максимум за раз.
                    $numPatients = SheduleByDay::model()->findAll('doctor_id = :doctor_id AND patient_day = :patient_day', array(':doctor_id' => $doctorId, ':patient_day' => $formatDate));
                    $resultArr[$i - 1]['numPatients'] = count($numPatients);
                    $resultArr[$i - 1]['quote'] = $settings['quote'];
                } else {
                    $resultArr[$i - 1]['worked'] = false;
                    $resultArr[$i - 1]['numPatients'] = 0;
                    $resultArr[$i - 1]['quote'] = 0;
                }
                $resultArr[$i - 1]['day'] = $i;
            }

            return $resultArr;
        }
    }

    public function actionGetPatientsListByDate() {
        if(Yii::app()->user->isGuest) {
            echo CJSON::encode(array('success' => 'false',
                                     'data' => 'Error!'));
            exit();
        }
        if(!isset($_GET['month'], $_GET['day'], $_GET['year'], $_GET['doctorid'])) {
            echo CJSON::encode(array('success' => 'false',
                                     'data' => 'Нехватка данных для выборки!'));
            exit();
        }
        $this->currentYear = $_GET['year'];
        $this->currentMonth = $_GET['month'];
        $this->currentDay = $_GET['day'];

        $patientsList = array();
        $sheduleByDay = new SheduleByDay();
        $formatDate = $this->currentYear.'-'.$this->currentMonth.'-'.$this->currentDay;
        $weekday = date('w', strtotime($formatDate)); // День недели (число)
        $patients = $sheduleByDay->getRows($formatDate, $_GET['doctorid']);

        // Теперь строим список пациентов и свободных ячеек исходя из выборки. Выбираем начало и конец времени по расписанию у данного врача
        $user = User::model()->findByPk(Yii::app()->user->id);
        if($user == null) {
            echo CJSON::encode(array('success' => 'false',
                                     'data' => 'Ошибка! Неавторизованный пользователь.'));
        }
        if($user['employee_id'] == null) {
            echo CJSON::encode(array('success' => 'false',
                                     'data' => 'Ошибка! К пользователю не прикреплён сотрудник.'));
        }
        $sheduleElements = SheduleSetted::model()->findAll('employee_id = :employee_id
                                                       AND (weekday = :weekday OR day = :day)',
                                                      array(
                                                          ':employee_id' => $user['employee_id'],
                                                          ':weekday' => $weekday,
                                                          ':day' => $formatDate
                                                      ));

        $settings = $this->getSettings();
        // Выясняем время работы. Частные дни имеют приоритет по сравнению с обычными
        $choosedType = 0;
        foreach($sheduleElements as $sheduleElement) {
            if($choosedType == 0 && $sheduleElement['type'] >= $choosedType) {
                $timestampBegin = strtotime($sheduleElement['time_begin']);
                $timestampEnd = strtotime($sheduleElement['time_end']);
                $choosedType = $sheduleElement['type']; // Далее можно выбрать только частный день
            }
        }
        $increment = $settings['timePerPatient'] * 60;
        $result = array();

        for($i = $timestampBegin; $i < $timestampEnd; $i += $increment) {
            // Ищем пациента для такого времени. Если он найден, значит время занято
            $isFound = false;
            foreach($patients as $key => $patient) {
                $timestamp = strtotime($patient['patient_time']);
                if($timestamp == $i) {
                    $result[] = array(
                        'timeBegin' => date('G:i', $i),
                        'timeEnd' => date('G:i', $i + $increment),
                        'fio' => $patient['fio'],
                        'isAllow' => 0, // Доступно ли время для записи или нет,
                        'id' => $patient['id'],
                        'cardNumber' => $patient['card_number']
                    );
                    $isFound = true;
                }
            }
            if(!$isFound) {
                $result[] = array(
                    'timeBegin' => date('G:i', $i),
                    'timeEnd' => date('G:i', $i + $increment),
                    'isAllow' => 1,
                    'fio' => '',
                    'id' => null,
                    'cardNumber' => null
                );
            }
        }
        echo CJSON::encode(array('success' => 'true',
                                 'data' => $result));
    }

    // Записать пациента на приём
    public function actionWritePatient() {
        if(!Yii::app()->request->isAjaxRequest) {
            echo "Error!";
            exit();
        }
        if(!isset($_GET['day'], $_GET['month'], $_GET['year'], $_GET['doctor_id'], $_GET['time'])) {
            echo "Error! Not enough data for request.";
            exit();
        }
        $formatDate = $_GET['year'].'-'.$_GET['month'].'-'.$_GET['day'];
        $formatTime = $_GET['time'];
        $sheduleElement = new SheduleByDay();
        $sheduleElement->doctor_id = $_GET['doctor_id'];
        $sheduleElement->medcard_id = $_GET['card_number'];
        $sheduleElement->patient_day = $formatDate;
        $sheduleElement->is_accepted = 0;
        $sheduleElement->patient_time = $formatTime;
        if(!$sheduleElement->save()) {
            echo CJSON::encode(array('success' => 'false',
                                     'data' => 'Не могу записать пациента!'));
            exit();
        }
        echo CJSON::encode(array('success' => 'true',
                                 'data' => 'Пациент успешно записан!'));
    }
    // Отписать пациента от приёма
    public function actionUnwritePatient() {
        if(!isset($_GET['id'])) {
            echo CJSON::encode(array('success' => 'false',
                                     'data' => 'Не могу отписать пациента от приёма!'));
            exit();
        }
        $sheduleElement = SheduleByDay::model()->findByPk($_GET['id']);
        if($sheduleElement != null) {
            $sheduleElement->delete();
        }

        echo CJSON::encode(array('success' => 'true',
                                 'data' => 'Пациент успешно отписан!'));
    }
}

?>