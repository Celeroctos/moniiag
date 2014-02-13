<?php
class PrintController extends Controller {
    public $layout = 'print';
    public $responseData = array();

    // Печать главной страницы карты
    public function actionPrintMainPage() {
        // Выбираем всю информацию о медкарте
        if(isset($_GET['medcardid'])) {

        }
        $medcard = Medcard::model()->findByPk($_GET['medcardid']);
        if($medcard == null) {
            exit('Ошибка! Не выбрана медкарта.');
        }
        if($medcard['invalid_group'] != 0 && $medcard['invalid_group'] != null) {
            $groups = array('I', 'II', 'III', 'IV');
            $medcard['invalid_group'] = $groups[$medcard['invalid_group']].' группа';
        } else {
            $medcard['invalid_group'] = 'Нет группы';
        }
        // Выбираем ОМС по медкарте
        $oms = Oms::model()->findByPk($medcard->policy_id);
        if($oms == null) {
            exit('Ошибка! Полиса не существует!');
        }
        // Выбираем предприятие по коду заведения в медкарте
        $enterprise = Enterprise::model()->findByPk($medcard->enterprise_id);
        if($enterprise == null) {
            exit('Ошибка: учреждения не существует!');
        }
        // Выбираем льготы по ОМС
        $privileges = PatientPrivilegie::model()->findAll('patient_id = :patient_id', array(':patient_id' => $oms->id));
        if(count($privileges) == 0) {
            $privileges = array();
        }
        // Приводим дату к виду
        $oms['givedate'] = $this->formatDate($oms['givedate']);
        $oms['birthday'] = $this->formatDate($oms['birthday']);
        if($oms['enddate'] != null) {
            $oms['enddate'] = $this->formatDate($oms['enddate']);
        }
        foreach($privileges as &$priv) {
            $priv['docgivedate'] = $this->formatDate($priv['docgivedate']);
            $privModel = Privilege::model()->findByPk($priv->privilege_id);
            $priv['docname'] = '(Код '.$privModel->code.') '.$priv['docname'];
        }
        $this->render('index', array('medcard' => $medcard,
                                     'oms' => $oms,
                                     'enterprise' => $enterprise,
                                     'privileges' => $privileges));
    }

    public function formatDate($date) {
        $parts = explode('-', $date);
        return $parts[2].'.'.$parts[1].'.'.$parts[0];
    }

    // Печать результа приёма
    public function actionPrintGreeting($greetingIn = false) {
        if($greetingIn === false && !isset($_GET['greetingid'])) {
            exit('Ошибка: не выбран приём.');
        } else {
            $greetingId = $greetingIn !== false ? $greetingIn : $_GET['greetingid'];
        }
        // В противном случае, выбираем все элементы, изменённые во время приёма
        $changedElements = MedcardElementForPatient::model()->findAllPerGreeting($greetingId);
        if(count($changedElements) == 0) {
            // Единичная печать
            if($greetingIn === false) {
                exit('Во время этого приёма не было произведено никаких изменений!');
            } else {
                return array();
            }
        }
        $sortedArr = array();
        // Сортируем по категориям
        $greetingInfo = array();
        $resultArr = array();
        foreach($changedElements as $element) {
            $elementInfo = MedcardElement::model()->getOne($element['element_id']);
            // Не существует общей информации по приёму
            if(count($greetingInfo) == 0) {
                $greeting = SheduleByDay::model()->findByPk($greetingId);
                if($greeting == null) {
                    exit('Ошибка: такого приёма не существует!');
                } else {
                    $doctor = Doctor::model()->findByPk($greeting['doctor_id']);
                    $greetingInfo['doctor_fio'] = $doctor['last_name'].' '.$doctor['first_name'].' '.$doctor['middle_name'];
                    // Найдём медкарту, а по ней и пациента
                    $medcard = Medcard::model()->findByPk($greeting['medcard_id']);
                    $patient = Oms::model()->findByPk($medcard['policy_id']);
                    $greetingInfo['patient_fio'] = $patient['last_name'].' '.$patient['first_name'].' '.$patient['middle_name'];
                    $greetingInfo['card_number'] = $greeting['medcard_id'];
                    $dateParts = explode('-', $greeting['patient_day']);
                    $greetingInfo['date'] = $dateParts[2].'.'.$dateParts[1].'.'.$dateParts[0];
                }
            }
            // Не существует категории с таким id - создаём
            $id = (string)$elementInfo['categorie_id'];
            if(!isset($resultArr[$id])) {
                $resultArr[$id] = array();
            }
            $num = count($resultArr[$id]);
            $resultArr[$id][$num]['element'] = $element;
            $resultArr[$id][$num]['info'] = $elementInfo;
            // Для комбо нужно посмотреть значение справочника
            if($elementInfo['guide_id'] != null) {
                if($elementInfo['type'] == 3) { // Комбо с множественным выбором
                    $values = CJSON::decode($element['value']);
                    // Клепаем строку из значений
                    $counter = 0;
                    foreach($values as $value) {
                        $valueSearched = MedcardGuideValue::model()->findByPk($value);
                        if($valueSearched != null) {
                            if($counter == 0) {
                                $resultArr[$id][$num]['element']['value'] = $valueSearched['value'].', ';
                            } else {
                                $resultArr[$id][$num]['element']['value'] .= $valueSearched['value'].', ';
                            }
                            $counter++;
                        }
                    }
                    $resultArr[$id][$num]['element']['value'] = substr($resultArr[$id][$num]['element']['value'], 0, count($resultArr[$id][$num]['element']['value']) - 3);
                } else {
                    $value = MedcardGuideValue::model()->findByPk($element['value']);
                    if($value != null) {
                        $resultArr[$id][$num]['element']['value'] = $value['value'];
                    }
                }
            }
        }
        // Рендерится, если приём один, если приёмов несколько (массПечать), то просто возвращается
        if($greetingIn === false) {
            $this->render('greeting', array(
                'categories' => $resultArr,
                'greeting' => $greetingInfo
            ));
        } else {
            return array(
                'categories' => $resultArr,
                'greeting' => $greetingInfo
            );
        }
    }

    // Массовая печать результатов приёма
    public function actionMassPrintGreetings() {
        if(!isset($_GET['greetingids'])) {
            exit('Ошибка: на обнаружено документов для печати.');
        }
        $greetings = CJSON::decode($_GET['greetingids']);
        if(count($greetings) == 0) {
            exit('Не выбраны документы для печати!');
        } else {
            $response = array();
            foreach($greetings as $greeting) {
                $result = $this->actionPrintGreeting($greeting);
                if(count($result) > 0) {
                    $response[] = $result;
                }
            }
        }
        //echo "<pre>";
       // var_dump($response);
       // exit();
        $this->render('massprintonelist', array(
            'greetings' => $response
        ));
    }

    // Получить данные для вьюхи
    public function actionMakePrintListView() {
        $patients = CJSON::decode($_GET['patients']);
        $doctors = CJSON::decode($_GET['doctors']);
        $numPatients = count($patients);
        $numDoctors = count($doctors);
        $resultArr = array();
        for($i = 0; $i < $numPatients; $i++) {
            for($j = 0; $j < $numDoctors; $j++) {
                // Теперь получаем все приёмы по врачу, пациенту и дате
                if(isset($_GET['date']) && trim($_GET['date']) != '') {
                   $greetings = SheduleByDay::model()->getGreetingsPerQrit($patients[$i], $doctors[$j], $_GET['date']);
                } else {
                   $greetings = SheduleByDay::model()->getGreetingsPerQrit($patients[$i], $doctors[$j]);
                }
                if(count($greetings) > 0) {
                    foreach($greetings as &$greeting) {
                        $parts = explode('-', $greeting['patient_day']);
                        $greeting['patient_day'] = $parts[2].'.'.$parts[1].'.'.$parts[0];
                        // Посмотрим, есть ли хоть какие-то изменения в медкарте за приём
                        $medcardChanges = MedcardElementForPatient::model()->findAll('greeting_id = :greeting_id', array('greeting_id' => $greeting['id']));
                        $greeting['num_changes'] = count($medcardChanges);
                        array_push($resultArr, $greeting);
                    }
                }
            }
        }

        echo CJSON::encode(array('success' => 'true',
                                 'data' => $resultArr));
    }

    // Получить страницу массовой печати
    public function actionMassPrintView() {
        $this->layout = 'index';
        $this->render('massprint', array());
    }

    // Распечтать страницу расписания
    public function actionShedulePrint() {
        $this->layout = 'shedule';
        $this->render('shedule', array());
    }
}
?>