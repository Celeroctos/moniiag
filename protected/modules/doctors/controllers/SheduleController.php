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

        $this->render('index', array(
            'patients' => $patients,
            'patientsInCalendar' => $patientsInCalendar,
            'currentPatient' => $this->currentPatient,
            'pregnantContent' => '',
            'filterModel' => $this->filterModel,
            'medcard' => isset($medcard) ? $medcard : null
        ));
    }

    // Получить даты, в которых у врача есть пациенты
    private function getDaysWithPatients() {
        $shedule = new SheduleByDay();
        return $shedule->getDaysWithPatients(Yii::app()->user->id);
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
                // Дальше смотрим, есть ли уже такой элемент в базе для конкретного пациента. Если есть - будем апдейтить. Если нет - писать.
                $element = MedcardElementForPatient::model()->find('element_id = :element_id AND medcard_id = :medcard_id', array(':medcard_id' => $_POST['FormTemplateDefault']['medcardId'],
                                                                                                                                  ':element_id' => $element->id)
                                                                  );
                if($element == null) {
                    $elementModel = new MedcardElementForPatient();
                    $elementModel->medcard_id = $_POST['FormTemplateDefault']['medcardId'];
                    $elementModel->element_id = $resArr[1];
                    $elementModel->value = $value;
                    if(!$elementModel->save()) {
                        echo CJSON::encode(array('success' => true,
                                                 'text' => 'Ошибка сохранения новой записи.'));
                        exit();
                    }
                } else {
                    $element->value = $value;
                    if(!$element->save()) {
                        echo CJSON::encode(array('success' => true,
                                                 'text' => 'Ошибка сохранения записи.'));
                        exit();
                    }
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
        $this->filterModel->date = $date;
        $doctorId = Yii::app()->user->id;
        // Выбираем пациентов на обозначенный день
        $sheduleByDay = new SheduleByDay();
        $patients = $sheduleByDay->getRows($date, $doctorId);
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
                                     'year' => $this->currentYear
                                 )
                        )
        );
    }

    private function getSettings() {
        $settings = Setting::model()->findAll('module_id = 1
                                                    AND name IN(\'timePerPatinet\',
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
            }
            if(isset($_GET['month'])) {
                $this->currentYear = $_GET['month'];
            }
            if(isset($_GET['day'])) {
                $this->currentYear = $_GET['day'];
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
                $dayEnd = date('t', $this->currentYear.'-'.$this->currentMonth);
            } else {
                $dayEnd = date('t');
                $this->currentYear = date('Y');
                $this->currentMonth = date('n');
                $this->currentDay = date('j');
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
                    $numPatients = SheduleByDay::model()->findAll('doctor_id = :doctor_id AND patient_time = :patient_time', array(':doctor_id' => $doctorId, ':patient_time' => $formatDate));
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
}

?>