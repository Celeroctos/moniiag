<?php
class SheduleController extends Controller {
    public $layout = 'index';
    public $filterModel = null;
    public $currentPatient = false;

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
}

?>