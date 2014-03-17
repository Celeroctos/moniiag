<?php
class SheduleController extends Controller {
    public $layout = 'index';
    public $filterModel = null;
    public $currentPatient = false;
    public $currentSheduleId = false;
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
            if(isset($_GET['rowid']) && trim($_GET['rowid']) != '') {
                $this->currentSheduleId = trim($_GET['rowid']);
                $greeting = SheduleByDay::model()->findByPk($_GET['rowid']);
                if(isset($_POST['templatesList'])) {
                    $templatesChoose = 0;
                    // Установленные диагнозы: первичный и сопутствующие. Это может быть просмотр приёма, который уже был, типа
                    $primaryDiagnosis = PatientDiagnosis::model()->findDiagnosis($_GET['rowid'], 0);
                    $secondaryDiagnosis = PatientDiagnosis::model()->findDiagnosis($_GET['rowid'], 1);
                    // Если приём был, то можно вынуть примечание к диагнозам
                    if($greeting != null) {
                        $note = $greeting->note;
                        // Пациента начали принимать, но он не принят: карту можно редактировать. В противном случае редактирование карты должно быть заблокировано
                        if($greeting->is_beginned != 1) {
                            $greeting->is_beginned = 1;
                            $greeting->time_begin = date('h:j');
                            if(!$greeting->save()) {
                                echo CJSON::encode(array('success' => true,
                                                         'text' => 'Ошибка сохранения записи.'));
                            }
                        }

                        if($greeting->is_beginned && !$greeting->is_accepted) {
                            $canEditMedcard = 1;
                        } else {
                            $canEditMedcard = 0;
                        }
                    }

                    $templatesList = $_POST['templatesList'];
                } else {
                    $canEditMedcard = 0;
                    $templatesChoose = 1;
                    $templatesList = MedcardTemplate::model()->findAll();
                }
            }
        }
        if(!isset($templatesChoose)) {
            $templatesChoose = 0;
        }

        // Если они не создались, это значит, что диагнозы пустые
        if(!isset($primaryDiagnosis, $secondaryDiagnosis)) {
            $primaryDiagnosis = array();
            $secondaryDiagnosis = array();
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
            'currentSheduleId' => $this->currentSheduleId,
            'pregnantContent' => '',
            'filterModel' => $this->filterModel,
            'medcard' => isset($medcard) ? $medcard : null,
            'currentDate' => $curDate,
            'addModel' => new FormValueAdd(),
            'historyPoints' => $this->getHistoryPoints(isset($medcard) ? $medcard : null),
            'primaryDiagnosis' => $primaryDiagnosis,
            'secondaryDiagnosis' => $secondaryDiagnosis,
            'note' => isset($note) ? $note : '',
            'canEditMedcard' => isset($canEditMedcard) ? $canEditMedcard : 0,
            'privilegesList' => $this->getPrivileges(),
            'modelMedcard' => new FormPatientWithCardAdd(),
            'modelOms' => new FormOmsEdit(),
            'currentTime' => date('Y-m-d h:m:s'),
            'templatesChoose' => $templatesChoose,
            'templatesList' => isset($templatesList) ? $templatesList : array(),
            'greeting' => (isset($greeting)) ? $greeting : null,
        ));
    }

    // Получить список льгот
    private function getPrivileges() {
        // Льготы
        $privilegeModel = new Privilege();
        $privilegesList = array('-1' => 'Нет');

        $privilegesListDb = $privilegeModel->getRows(false);
        foreach($privilegesListDb as $privilege) {
            $privilegesList[$privilege['id']] = $privilege['name'].' (Код '.$privilege['code'].')';
        }
        return $privilegesList;
    }

    public function actionGetHistoryPoints($medcardid) {
        $medcard = Medcard::model()->findByPk($medcardid);
        if($medcard == null) {
            echo CJSON::encode(array('success' => false,
                                     'error' => 'Не хватает данных для получения точек истории медкарты!'));
            exit();
        }
        $historyPoints = $this->getHistoryPoints($medcard);
        echo CJSON::encode(array('success' => true,
                                 'data' => $historyPoints));
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
            $currentDate = date('Y-m-d H:i');
            $answerCurrentDate = false;
            foreach($_POST['FormTemplateDefault'] as $field => $value) {
                if($field == 'medcardId' || $field == 'greetingId') {
                    continue;
                }

                // Это для выпадающего списка с множественным выбором
                if(is_array($value)) {
                    $value = CJSON::encode($value);
                }

                // Проверим, есть ли такое поле вообще
                if(!preg_match('/^f(\d+\|)*\d+_(\d+)$/', $field, $resArr)) {
                    continue;
                }

                // Берём и тупо находим элемент по пути
                // Смотрим: если в историю не занесена категория, то нужно занести с сохранением параметров
                // Находим путь
                $pathWithSeparators = mb_substr($field, 1, mb_strrpos($field, '_') - 1);
                $arrPath =  explode('|', $pathWithSeparators);
                $path = implode('.', $arrPath);

                $historyIdResult = MedcardElementForPatient::model()->getMaxHistoryPointId(
                    array('path' => $path),
                    $_POST['FormTemplateDefault']['medcardId'],
                    $_POST['FormTemplateDefault']['greetingId']
                );

                // Элемент. Если в историю не занесён, нужно заносить. И только тогда
                $historyCategorieElement = MedcardElementForPatient::model()->find('medcard_id = :medcard_id AND greeting_id = :greeting_id AND element_id = :element_id AND history_id = :history_id AND path = :path', array(
                    ':medcard_id' => $_POST['FormTemplateDefault']['medcardId'],
                    ':greeting_id' => $_POST['FormTemplateDefault']['greetingId'],
                    ':element_id' => $resArr[count($resArr) - 1],
                    ':history_id' => $historyIdResult['history_id_max'],
                    ':path' => $path
                ));

                // Дальше смотрим, есть ли уже такой элемент в базе для конкретного пациента. Если есть - будем апдейтить. Если нет - писать. Это позволит не сохранять неизменённые поля
                if($value == $historyCategorieElement->value) {
                    continue;
                }

                $historyCategorieElementNext = new MedcardElementForPatient();
                $historyCategorieElementNext->value = $value;
                $historyCategorieElementNext->history_id = $historyCategorieElement->history_id + 1;
                $historyCategorieElementNext->medcard_id = $historyCategorieElement->medcard_id;
                $historyCategorieElementNext->greeting_id = $historyCategorieElement->greeting_id;
                $historyCategorieElementNext->categorie_name = $historyCategorieElement->categorie_name;
                $historyCategorieElementNext->path = $historyCategorieElement->path;
                $historyCategorieElementNext->is_wrapped = $historyCategorieElement->is_wrapped;
                $historyCategorieElementNext->categorie_id = $historyCategorieElement->categorie_id;
                $historyCategorieElementNext->element_id = $historyCategorieElement->element_id;
                $historyCategorieElementNext->label_before = $historyCategorieElement->label_before;
                $historyCategorieElementNext->label_after = $historyCategorieElement->label_after;
                $historyCategorieElementNext->size = $historyCategorieElement->size;
                $historyCategorieElementNext->change_date = $currentDate;
                $historyCategorieElementNext->type = $historyCategorieElement->type;
				$historyCategorieElementNext->allow_add = $historyCategorieElement->allow_add;
                $historyCategorieElementNext->guide_id = $historyCategorieElement->guide_id;
                $historyCategorieElementNext->config = $historyCategorieElement->config;

                $answerCurrentDate = true;
                if(!$historyCategorieElementNext->save()) {
                    echo CJSON::encode(array('success' => true,
                                             'text' => 'Ошибка сохранения записи.'));
                    exit();
                }
            }

            $response = array(
                'success' => true,
                'text' => 'Данные успешно сохранены.'
            );

            if($answerCurrentDate) {
                $response['historyDate'] = $currentDate;
            }

            echo CJSON::encode($response);
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
            //exit('Error!');
        }
        // Выбираем пациентов на обозначенный день
        $sheduleByDay = new SheduleByDay();
        $patients = $sheduleByDay->getRows($date, $doctor['employee_id'], 0, 1);
        return $patients;
    }

    // Начать приём пациента
    public function actionAcceptBegin() {
        $req = new CHttpRequest();
        if(isset($_GET['id']) && trim($_GET['id']) != '') {
            // Записать, что пациент принят
            $sheduleElement = SheduleByDay::model()->findByPk($_GET['id']);
            if($sheduleElement != null) {
                $sheduleElement->is_beginned = 1;
                $sheduleElement->time_begin = date('h:j');
                if(!$sheduleElement->save()) {
                    echo CJSON::encode(array('success' => true,
                                             'text' => 'Ошибка сохранения записи.'));
                }
            }
        }

        $req->redirect($_SERVER['HTTP_REFERER']);
    }

    // Закончить приём пациента
    public function actionAcceptComplete() {
        $req = new CHttpRequest();
        if(isset($_GET['id']) && trim($_GET['id']) != '') {
            // Записать, что пациент принят
            $sheduleElement = SheduleByDay::model()->findByPk($_GET['id']);
            if($sheduleElement != null) {
                $sheduleElement->is_accepted = 1;
                $sheduleElement->time_end = date('h:j');
                // Записать статус медкарты: медкарта вернулась обратно в регистратуру
                $medcard = Medcard::model()->findByPk($sheduleElement->medcard_id);
                if($medcard != null) {
                    $medcard->motion = 0; // Сразу в регистратуру: человеческий фактор говорит о том, что связку врач-регистратура можно будет отловить по истории
                    if(!$medcard->save()) {
                        echo CJSON::encode(array('success' => false,
                                                 'text' => 'Ошибка сохранения статуса медкарты.'));
                    }
                }
                if(!$sheduleElement->save()) {
                    echo CJSON::encode(array('success' => false,
                                             'text' => 'Ошибка сохранения записи.'));
                }
            }
        }

        $req->redirect($_SERVER['HTTP_REFERER']);
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
            // Теперь вынем стабильное расписание выходных
            $restDays = SheduleRest::model()->findAll();
            $restDaysArr = array();
            foreach($restDays as $restDay) {
                $restDaysArr[] = $restDay->day;
            }

            // Теперь вынем все дни, которые являются праздничными: на них тоже нельзя записывать человеков
            $paramDate =  $this->currentYear.'-'.($this->currentMonth > 9 ? $this->currentMonth : '0'.$this->currentMonth);
            $restDaysLonely = SheduleRestDay::model()->findAll('substr(cast(date as text), 0, 8) = :date', array(':date' => $paramDate));
            $restDaysArrLonely = array();
            foreach($restDaysLonely as $dayLonely) {
                $parts = explode('-', $dayLonely->date);
                $restDaysArrLonely[] = (int)$parts[2];
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
                if((array_search($weekday, $usual) !== false && array_search($weekday, $restDaysArr) === false && array_search($i, $restDaysArrLonely) === false) || array_search($formatDate, $exps) !== false) {
                    // День существует, врач работает
                    $resultArr[$i - 1]['worked'] = true;
                    $resultArr[$i - 1]['restDay'] = false;
                    // Дальше, исходя из настроек, смотрим: полностью свободный, частично свободный или полностью занятый день
                    // TODO: в цикле очень плохо делать выборку. 31 выборка максимум за раз.
                    // Более глубокое сканирование: необходимо посмотреть, какие пациенты вообще есть в расписании по данным датам. Может получиться так, что при изменённом расписании потеряются пациенты
                    $timeStampCurrent = mktime(0, 0, 0);
                        if(strtotime($formatDate) >= $timeStampCurrent) {
                        $numPatients = $this->getPatientList($doctorId, $this->currentYear.'-'.$month.'-'.$day);
                        $resultArr[$i - 1]['numPatients'] = count(array_filter($numPatients['result'], function($element) {
                            return $element['id'] != null;
                        }));
                        // Если мест реально меньше, чем квота (у врача укороченная смена, либо текущий день и середина смены, скажем)
                        if($numPatients['numPlaces'] < $settings['quote']) {
                            $resultArr[$i - 1]['quote'] = $numPatients['numPlaces'];
                        } else {
                            $resultArr[$i - 1]['quote'] = $settings['quote'];
                        }
                    } else {
                        $resultArr[$i - 1]['quote'] = $settings['quote'];
                        $resultArr[$i - 1]['numPatients'] = 0;
                    }
                    // Квота изменяется вручную: возможно, врач просто не успеет за смену принять квоту человек
                    // Если врач работает в этот день, надо посмотреть, не прошедшая ли дата. На прошедшие даты записывать не надо.
                    $timeStampPerIteration = mktime(0, 0, 0, $month, $day, $this->currentYear);
                    // Если время итерируемое больше, то на такие числа записывать можно
                    if($timeStampCurrent <= $timeStampPerIteration) {
                        $resultArr[$i - 1]['allowForWrite'] = 1;
                    } else {
                        $resultArr[$i - 1]['allowForWrite'] = 0;
                    }
                } else {
                    // Если это выходной, его тоже нужно помечать
                    if(array_search($weekday, $restDaysArr) !== false || array_search($i, $restDaysArrLonely) !== false) {
                        $resultArr[$i - 1]['restDay'] = true;
                    } else {
                        $resultArr[$i - 1]['restDay'] = false;
                    }
                    $resultArr[$i - 1]['worked'] = false;
                    $resultArr[$i - 1]['numPatients'] = 0;
                    $resultArr[$i - 1]['quote'] = 0;
                    $resultArr[$i - 1]['allowForWrite'] = 0;
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
        $result = $this->getPatientList($_GET['doctorid'], $this->currentYear.'-'.$this->currentMonth.'-'.$this->currentDay);

        echo CJSON::encode(array('success' => 'true',
                                 'data' => $result['result']));
    }

    private function getPatientList($doctorId, $formatDate) {
        $patientsList = array();
        $sheduleByDay = new SheduleByDay();
        $weekday = date('w', strtotime($formatDate)); // День недели (число)
        $patients = $sheduleByDay->getRows($formatDate, $doctorId);

        // Теперь строим список пациентов и свободных ячеек исходя из выборки. Выбираем начало и конец времени по расписанию у данного врача
        $user = User::model()->findByPk(Yii::app()->user->id);
        if($user == null) {
            echo CJSON::encode(array('success' => 'false',
                'data' => 'Ошибка! Неавторизованный пользователь.'));
        }
        $sheduleElements = SheduleSetted::model()->findAll('employee_id = :employee_id
AND (weekday = :weekday OR day = :day)',
            array(
                ':employee_id' => $doctorId,
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
        $numRealPatients = 0; // Это для того, чтобы понять, заполнено ли всё
        $currentTimestamp = time();
        $parts = explode('-', $formatDate);
        $today = ($parts[0] == date('Y') && $parts[1] == date('n') && $parts[2] == date('j'));
        for($i = $timestampBegin; $i < $timestampEnd; $i += $increment) {
            if($currentTimestamp >= $i && $today) {
                continue;
            }
            // Ищем пациента для такого времени. Если он найден, значит время занято
            $isFound = false;
            foreach($patients as $key => $patient) {
                $timestamp = strtotime($patient['patient_time']);
                if($timestamp == $i) {
                    // Если пациент опосредованный, для него надо выбрать ФИО
                    if($patient['mediate_id'] != null) {
                        $mediatePatient = MediatePatient::model()->findByPk($patient['mediate_id']);
                        if($mediatePatient != null) {
                            $patient['fio'] = $mediatePatient['last_name'].' '.$mediatePatient['first_name'].' '.$mediatePatient['middle_name'].' (опосредованный)';
                        }
                    }
                    $result[] = array(
                        'timeBegin' => date('G:i', $i),
                        'timeEnd' => date('G:i', $i + $increment),
                        'fio' => $patient['fio'],
                        'isAllow' => 0, // Доступно ли время для записи или нет,
                        'id' => $patient['id'],
                        'type' => $patient['mediate_id'] != null ? 1 : 0,
                        'cardNumber' => $patient['card_number']
                    );
                    $isFound = true;
                    $numRealPatients++;
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

        return array(
                'result' => $result,
                'allReserved' => $numRealPatients == count($result),
                'numPlaces' => count($result)
        );
    }

    // Записать пациента на приём
    public function actionWritePatient() {
        if(!Yii::app()->request->isAjaxRequest) {
            echo "Error!";
            exit();
        }
        if(!isset($_GET['day'], $_GET['month'], $_GET['year'], $_GET['doctor_id'], $_GET['time'], $_GET['mode'])) {
            echo "Error! Not enough data for request.";
            exit();
        }
        $formatDate = $_GET['year'].'-'.$_GET['month'].'-'.$_GET['day'];
        $formatTime = $_GET['time'];
        // Вынимаем элемент расписания для записи кабинета, например
        // Определим день
        $weekday = date('w', strtotime($formatDate));
        $sheduleSetted = SheduleSetted::model()->find('weekday = :weekday AND employee_id = :employee_id AND day IS NULL', array(':weekday' => $weekday, ':employee_id' => $_GET['doctor_id']));
        $sheduleElement = new SheduleByDay();
        $sheduleElement->doctor_id = $_GET['doctor_id'];
        $sheduleElement->patient_day = $formatDate;
        $sheduleElement->is_accepted = 0;
        $sheduleElement->patient_time = $formatTime;
        if($sheduleSetted != null) {
            $sheduleElement->shedule_id = $sheduleSetted->id;
        }
        if($_GET['mode'] == 0) { // Обычная запись
            $sheduleElement->medcard_id = $_GET['card_number'];
            $sheduleElement->mediate_id = null;
        } elseif($_GET['mode'] == 1) { // Опосредованная запись
            $sheduleElement->medcard_id = null;
            // Создаём запись опосредованного пациента
            $mediate = new MediatePatient();
            $mediateForm = new FormMediatePatientAdd();
            $mediateForm->attributes = $_GET;
            if(!$mediateForm->validate()) {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' =>  $mediateForm->getErrors()));
                exit();
            }
            // Заполняем значениями форму опосредованного пациента

            $mediate->first_name = $mediateForm->firstName;
            $mediate->last_name = $mediateForm->lastName;
            $mediate->middle_name = $mediateForm->middleName;
            $mediate->phone = $mediateForm->phone;

            if(!$mediate->save()) {
                echo CJSON::encode(array('success' => 'false',
                                         'error' =>  'Не могу сохранить опосредованного пациента в базе!'));
                exit();
            }
            $sheduleElement->mediate_id = $mediate->id;
        }
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