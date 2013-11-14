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
                $model = new FormPatientWithCardAdd();
                $this->render('addPatientWithCard', array(
                    'model' => $model,
                    'policy_number' => $patient->oms_number,
                    'policy_id' => $patient->id,
                    'fio' => $patient->first_name.' '.$patient->last_name.' '.$patient->middle_name
                ));
            } else {
                $model = new FormPatientAdd();
                $this->render('addPatientWithoutCard', array(
                    'model' => $model
                ));
            }
        } else {
            $model = new FormPatientAdd();
            $this->render('addPatientWithoutCard', array(
                'model' => $model
            ));
        }
    }

    // Добавление пациента
    public function actionAdd() {
        $model = new FormPatientAdd();
        if(isset($_POST['FormPatientAdd'])) {
            $model->attributes = $_POST['FormPatientAdd'];
            if($model->validate()) {
                $oms = new Oms();
                $medcard = new Medcard();

                $this->addEditModelOms($oms, $model);
                $this->addEditModelMedcard($medcard, $model, $oms);

                echo CJSON::encode(array('success' => 'true',
                                         'msg' => 'Новая запись успешно добавлена!'));
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    // Добавление карты к существующему пациенту
    public function actionAddCard() {
        $model = new FormPatientWithCardAdd();
        if(isset($_POST['FormPatientWithCardAdd'])) {
            $model->attributes = $_POST['FormPatientWithCardAdd'];
            if($model->validate()) {
                $oms = Oms::model()->findByPk($model->policy);
                $medcard = new Medcard();

                $this->addEditModelMedcard($medcard, $model, $oms);

                echo CJSON::encode(array('success' => 'true',
                                         'msg' => 'Новая запись успешно добавлена!'));
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    // Добавление полиса
    private function addEditModelOms($oms, $model) {
        $oms->first_name = $model->firstName;
        $oms->last_name = $model->lastName;
        $oms->middle_name = $model->middleName;
        $oms->oms_number = $model->policy;
        $oms->gender = $model->gender;
        $oms->birthday = $model->birthday;

        if(!$oms->save()) {
            echo CJSON::encode(array('success' => true,
                                     'error' => 'Произошла ошибка записи нового полиса.'));
            exit();
        }

        return true;
    }

    // Редактирование карты, вьюха
    public function actionEditCardView() {
        if(isset($_GET['cardid'])) {
            $modelCard = new Medcard();
            $medcard = $modelCard->findByPk($_GET['cardid']);
            if($medcard == null) {
                $req = new CHttpRequest();
                $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/viewsearch'));
            }
            $modelOms = new Oms();
            $oms = $modelOms->findByPk($medcard->policy_id);

            if($oms != null) {
                $formModel = new FormPatientWithCardAdd();

                // Заполняем модель
                $formModel->serie = $medcard->serie;
                $formModel->snils = $medcard->snils;
                $formModel->address = $medcard->address;
                $formModel->addressReg = $medcard->address_reg;
                $formModel->doctype = $medcard->doctype;
                $formModel->docnumber = $medcard->docnumber;
                $formModel->whoGived = $medcard->who_gived;
                $formModel->documentGivedate = $medcard->gived_date;
                $formModel->invalidGroup = $medcard->invalid_group;
                $formModel->workPlace = $medcard->work_place;
                $formModel->workAddress = $medcard->work_address;
                $formModel->post = $medcard->post;
                $formModel->contact = $medcard->contact;
                $formModel->cardNumber = $medcard->card_number;

                $this->render('editMedcard', array(
                    'model' => $formModel,
                    'policy_number' => $oms->oms_number,
                    'policy_id' => $oms->id,
                    'card_number' => $medcard->card_number,
                    'fio' => $oms->first_name.' '.$oms->last_name.' '.$oms->middle_name
                ));
            }
        }
    }

    // Редактирование полиса (ОМС), вьюха
    public function actionEditOmsView() {
        if(isset($_GET['omsid'])) {
            $modelOms = new Oms();
            $oms = $modelOms->findByPk($_GET['omsid']);
            if($oms == null) {
                $req = new CHttpRequest();
                $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/viewsearch'));
            }

            if($oms != null) {
                $formModel = new FormOmsEdit();
                $formModel->firstName = $oms->first_name;
                $formModel->lastName = $oms->last_name;
                $formModel->middleName = $oms->middle_name;
                $formModel->policy = $oms->oms_number;
                $formModel->gender = $oms->gender;
                $formModel->birthday = $oms->birthday;
                $formModel->id = $oms->id;

                $this->render('editOms', array(
                    'model' => $formModel,
                    'policy_number' => $oms->oms_number,
                    'policy_id' => $oms->id,
                    'fio' => $oms->first_name.' '.$oms->last_name.' '.$oms->middle_name
                ));
            }
        }
    }

    // Редактирование карты
    public function actionEditCard() {
        $model = new FormPatientWithCardAdd();
        if(isset($_POST['FormPatientWithCardAdd'])) {
            $model->attributes = $_POST['FormPatientWithCardAdd'];
            if($model->validate()) {
                $medcard = Medcard::model()->findByPk($_POST['FormPatientWithCardAdd']['cardNumber']);
                $this->addEditModelMedcard($medcard, $model);
                echo CJSON::encode(array('success' => 'true',
                                         'msg' => 'Запись успешно отредактирована.'));
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    // Редактирование ОМС
    public function actionEditOms() {
        $model = new FormOmsEdit();
        if(isset($_POST['FormOmsEdit'])) {
            $model->attributes = $_POST['FormOmsEdit'];
            if($model->validate()) {
                $oms = Oms::model()->findByPk($_POST['FormOmsEdit']['id']);
                $this->addEditModelOms($oms, $model);
                echo CJSON::encode(array('success' => 'true',
                                         'msg' => 'Запись успешно отредактирована.'));
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    // Добавление медкарты
    private function addEditModelMedcard($medcard, $model, $oms = false) {
        // Добавление карты: нет id
        if($medcard->card_number == null) {
            $medcard->card_number = $this->getCardNumber();
        }
        $medcard->snils = $model->snils;
        $medcard->address = $model->address;
        $medcard->address_reg = $model->addressReg;
        $medcard->doctype = $model->doctype;
        $medcard->serie = $model->serie;
        $medcard->docnumber = $model->docnumber;
        $medcard->who_gived = $model->whoGived;
        $medcard->gived_date = $model->documentGivedate;
        $medcard->invalid_group = $model->invalidGroup;
        $medcard->reg_date = date('Y-m-d');
        $medcard->work_place = $model->workPlace;
        $medcard->work_address = $model->workAddress;
        $medcard->post = $model->post;
        $medcard->contact = $model->contact;

        if($oms) {
            $medcard->policy_id = $oms->id;
        }

        if(!$medcard->save()) {
            echo CJSON::encode(array('success' => true,
                                     'error' => 'Произошла ошибка записи новой медкарты.'));
            exit();
        }

        return true;
    }

    // Генерация номера карты
    private function getCardNumber() {
        // Формат номера DDDDD/YY, где DDDDD – уникальный номер в разрезе года, YY  - 2 цифры года.
        $year = date('Y');
        $code = substr($year, mb_strlen($year) - 2);

        $medcard = new Medcard();
        $last = $medcard->getLastMedcardPerYear($code);
        if(count($last) == 0) {
            $idPerYear = 1;
        } else {
            $parts = explode('/', $last[0]['card_number']);
            $idPerYear = ++$parts[0];
        }

        return $idPerYear.'/'.$code;
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