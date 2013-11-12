<?php
class PatientController extends Controller {
    public $layout = 'application.views.layouts.index';

    // Стартовая
    public function actionIndex() {
        $this->render('index', array());
    }

    // Просмотр страницы поиска пациента
    public function actionViewSearch() {
        $this->render('searchPatient', array());
    }

    // Просмотр страницы добавления пациента
    public function actionViewAdd() {
        if(isset($_GET['patientid'])) {
            $model = new Oms();
            $patient = $model->findByPk($_GET['patientid']);
            // Скрыть частично поля, которые не нужны при первичной регистрации
            if($patient != null) {
                $this->render('addPatientWithCard', array(
                    'policy_number' => $patient->oms_number,
                    'fio' => $patient->first_name.' '.$patient->last_name.' '.$patient->middle_name
                ));
            } else {
                $this->render('addPatientWithoutCard', array(
                ));
            }
        } else {
            $this->render('addPatientWithoutCard', array(
            ));
        }
    }

    // Поиск пациента и его запись
    public function actionSearch() {
        if(!isset($_GET['filters']) || trim($_GET['filters']) == '') {
            echo CJSON::encode(array('success' => false,
                                     'data' => 'Задан пустой поисковой запрос.')
            );
            exit();
        }

        $filters = CJSON::decode($_GET['filters']);
        $allEmpty = true;
        foreach($filters['rules'] as $key => $filter) {
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

        $model = new Oms();
        $oms = $model->getRows($filters);
        $omsWith = array();
        $omsWithout = array();

        foreach($oms as $index => $item) {
            $parts = explode('-', $item['reg_date']);
            $item['reg_date'] = $parts[0];
            if($item['card_number'] == null) {
                $omsWithout[] = $item;
            } else {
                $omsWith[] = $item;
            }
        }

        echo CJSON::encode(array('success' => true,
                                 'data' => array('without' => $omsWithout,
                                                 'with' => $omsWith)
        ));
    }
}


?>