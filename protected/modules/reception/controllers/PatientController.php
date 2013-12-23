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

    // Просмотр страницы добавления карты к пациенту
    public function actionViewAdd() {
        $privilegesList = $this->getPrivileges();

        if(isset($_GET['patientid'])) {
            $model = new Oms();
            $patient = $model->findByPk($_GET['patientid']);
            // Скрыть частично поля, которые не нужны при первичной регистрации
            if($patient != null) {
                // Нужно найти последнюю медкарту, чтобы по ней заполнить данными
                $medcardModel = new Medcard();
                $medcard = $medcardModel->getLastByPatient($patient->id);
                $formModel = new FormPatientWithCardAdd();
                // Ищем модель, если карта есть
                if($medcard != null) {
                    $medcard = Medcard::model()->findByPk($medcard['card_number']);
                    $this->fillFormMedcardModel($formModel, $medcard);
                }
                // Ищем привилегии
                $privileges = PatientPrivilegie::model()->findAll('patient_id = :patient_id', array(':patient_id' => $medcard->policy_id));

                if(count($privileges) > 0) {
                    // TODO: пока говорим о том, что одному пациенту соответствует одна привилегия. В будущем будем писать для целого массива льгот
                    $this->fillPrivilegeFormPart($formModel, $privileges);
                }

                $this->render('addPatientWithCard', array(
                    'model' => $formModel,
                    'policy_number' => $patient->oms_number,
                    'policy_id' => $patient->id,
                    'fio' => $patient->first_name.' '.$patient->last_name.' '.$patient->middle_name,
                    'regPoint' => date('Y'),
                    'privilegesList' => $privilegesList,
                    'foundPriv' => count($privileges) > 0
                ));
            } else {
                $model = new FormPatientAdd();
                $this->render('addPatientWithoutCard', array(
                    'model' => $model,
                    'regPoint' => date('Y'),
                    'privilegesList' => $privilegesList,
                    'foundPriv' => false
                ));
            }
        } else {
            $model = new FormPatientAdd();
            $this->render('addPatientWithoutCard', array(
                'model' => $model,
                'regPoint' => date('Y'),
                'privilegesList' => $privilegesList,
                'foundPriv' => false
            ));
        }
    }

    // Добавление пациента
    public function actionAdd() {
        $model = new FormPatientAdd();
        if(isset($_POST['FormPatientAdd'])) {
            $model->attributes = $_POST['FormPatientAdd'];
			
            if($model->validate()) {
                $this->checkUniqueOms($model);
                $this->checkUniqueMedcard($model);
                $oms = new Oms();
                $medcard = new Medcard();

                $this->addEditModelOms($oms, $model);
                $this->addEditModelMedcard($medcard, $model, $oms);

                if($_POST['privilege'] != -1) {
                    $patientPrivelege = new PatientPrivilegie();
                    $this->addEditModelPrivilege($patientPrivelege, $model, $oms->id);
                }

                echo CJSON::encode(array('success' => 'true',
                                         'msg' => 'Новая запись успешно добавлена!',
                                         'cardNumber' => $medcard->card_number));
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    // Проверка на уникальность данных в медкарте
    private function checkUniqueMedcard($model) {
        // На момент создания пациента не должно быть идентичного с номером СНИЛС и паспортом (серии + номер)
        if(trim($model->snils) != '') {
            $medcardSearched = Medcard::model()->find('snils = :snils OR (docnumber = :docnumber AND serie = :serie)', array(
                ':snils' => $model->snils,
                ':docnumber' => $model->docnumber,
                ':serie' => $model->serie)
            );
        } else {
            $medcardSearched = Medcard::model()->find('docnumber = :docnumber AND serie = :serie', array(
                ':docnumber' => $model->docnumber,
                ':serie' => $model->serie)
            );
        }
        if($medcardSearched != null) {
            echo CJSON::encode(array('success' => 'false',
                'errors' => array(
                    'docnumber' => array(
                        'Такое сочетание серия-номер паспорта или номер СНИЛС уже есть в базе!'
                    )
                )));
            exit();
        }
    }

    private function checkUniqueOms($model) {
        // Проверим, не существует ли уже такого ОМС
        $omsSearched = Oms::model()->find('oms_number = :oms_number', array(':oms_number' => $model->policy));
        if($omsSearched != null) {
            echo CJSON::encode(array('success' => 'false',
                'errors' => array(
                    'policy' => array(
                        'Такой номер ОМС уже существует в базе!'
                    )
                )));
            exit();
        }
    }

    // Добавление карты к существующему пациенту
    public function actionAddCard() {
        $model = new FormPatientWithCardAdd();
        if(isset($_POST['FormPatientWithCardAdd'])) {
            $model->attributes = $_POST['FormPatientWithCardAdd'];
            if($model->validate()) {
                $oms = Oms::model()->findByPk($model->policy);
                // Проверим, нет ли карты с таким годом и с таким пациентом
                $year = date('Y');
                $code = substr($year, mb_strlen($year) - 2);
                $medcard = new Medcard();
                $medcardSearched = $medcard->getLastMedcardPerYear($code, $oms->id);
                if($medcardSearched != null) {
                    echo CJSON::encode(array('success' => 'false',
                        'errors' => array(
                            'id' => array(
                                'Карта для данного пациента в этом году уже создана!'
                            )
                        )));
                    exit();
                }

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
        $oms->type = $model->omsType;
        $oms->middle_name = $model->middleName;
        $oms->oms_number = $model->policy;
        $oms->gender = $model->gender;
        $oms->birthday = $model->birthday;
        $oms->givedate = $model->policyGivedate;
        if(trim($model->policyEnddate) != '') {
            $oms->enddate = $model->policyEnddate;
        }

        if(!$oms->save()) {
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'Произошла ошибка записи нового полиса.'));
            exit();
        }

        return true;
    }

    // Добавление льготы
    private function addEditModelPrivilege($privilege, $model, $patientId) {
        $privilege->patient_id = $patientId;
        $privilege->privilege_id = $model->privilege;
        $privilege->docname = $model->privDocname;
        $privilege->docnumber = $model->privDocnumber;
        $privilege->docserie = $model->privDocserie;
        $privilege->docgivedate = $model->privDocGivedate;

        if(!$privilege->save()) {
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'Произошла ошибка записи льготы для нового пациента.'));
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
                $this->fillFormMedcardModel($formModel, $medcard);

                $privileges = PatientPrivilegie::model()->findAll('patient_id = :patient_id', array(':patient_id' => $oms->id));
                if(count($privileges) > 0) {
                    // TODO: пока говорим о том, что одному пациенту соответствует одна привилегия. В будущем будем писать для целого массива льгот
                    $this->fillPrivilegeFormPart($formModel, $privileges);
                }
                $privilegesList = $this->getPrivileges();

                $this->render('editMedcard', array(
                    'model' => $formModel,
                    'policy_number' => $oms->oms_number,
                    'policy_id' => $oms->id,
                    'card_number' => $medcard->card_number,
                    'fio' => $oms->first_name.' '.$oms->last_name.' '.$oms->middle_name,
                    'privilegesList' => $privilegesList,
                    'foundPriv' => count($privileges) > 0
                ));
            }
        }
    }

    // Заполнение модели формы значениями
    private function fillFormMedcardModel($formModel, $medcard) {
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
        $formModel->profession = $medcard->profession;
    }

    // Записать в форму редактирования льготу
    private function fillPrivilegeFormPart($formModel, $privileges) {
        foreach($privileges as $privilege) {
            $formModel->privDocname = $privilege->docname ;
            $formModel->privDocnumber = $privilege->docnumber;
            $formModel->privDocserie = $privilege->docserie;
            $formModel->privDocGivedate = $privilege->docgivedate;
            $formModel->privilege = $privilege->privilege_id;
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
                $formModel->omsType = $oms->type;
                $formModel->policyGivedate = $oms->givedate;
                $formModel->policyEnddate = $oms->enddate;

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

                if($model->privilege != -1) {
                    $patientPrivelege = PatientPrivilegie::model()->findAll('patient_id = :patient_id', array(':patient_id' => $medcard->policy_id));
                    if(count($patientPrivelege) > 0) {
                        $this->addEditModelPrivilege($patientPrivelege, $model, $medcard->policy_id);
                    } else {
                        $this->addEditModelPrivilege(new PatientPrivilegie(), $model, $medcard->policy_id);
                    }
                } else { // Удалить все привилегии для пациента
                    $privs = PatientPrivilegie::model()->findAll('patient_id = :patient_id', array(':patient_id' => $medcard->policy_id));
                    foreach($privs as $priv) {
                        $priv->delete();
                    }
                }

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
        $medcard->profession = $model->profession;

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

    // Поиск пациента и его запсь
    public function actionSearch() {
        $oms = $this->searchPatients();
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

    private function searchPatients($filters = false) {
        if((!isset($_GET['filters']) || trim($_GET['filters']) == '') && (bool)$filters === false) {
            echo CJSON::encode(array('success' => false,
                                     'data' => 'Задан пустой поисковой запрос.')
            );
            exit();
        }

        $filters = CJSON::decode(isset($_GET['filters']) ? $_GET['filters'] : $filters);
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
        return $oms;
    }


    // Постановка на учёт беременных, вьюха
    public function actionAddPregnant() {
        $searchModel = $this->fillAndGetSearchFields();
        $addEditModel = $this->fillAndGetPregnantFields($searchModel->cardNumber);

        $doctorsList = array('-1' => 'Нет');
        // Получаем список врачей, которых можно прикрепить к пациентке
        $employeeModel = new Employee();
        $employees = $employeeModel->getRows(false, false, array(
            'groupOp' => 'AND',
            'rules' => array(
                array(
                    'field' => 'is_for_pregnants',
                    'op' => 'eq',
                    'data' => 1 // Всех, кто может обслуживать беременных
                )
            )
        ));
        foreach($employees as $employee) {
            $doctorsList[$employee['id']] = $employee['last_name'].' '.$employee['first_name'].' '.$employee['middle_name'].' ('.mb_strtolower($employee['ward'], 'UTF-8').' отделение, '.$employee['enterprise'].')';
        }

        echo $this->render('pregnant', array(
            'model' => $searchModel,
            'modelAddEdit' => $addEditModel,
            'doctorsList' => $doctorsList
        ));
    }

    // Постановка на учёт беременных: сам экшн формы
    public function actionAddEditPregnant() {
        $model = new FormPregnantAdd();
        if(isset($_POST['FormPregnantAdd'])) {
            $model->attributes = $_POST['FormPregnantAdd'];
            if($model->validate()) {
                $pregnant = Pregnant::model()->findByPk($_POST['FormPregnantAdd']['id']);
                if($pregnant == null) {
                    $pregnant = new Pregnant();
                }
                $this->addEditModelPregnant($pregnant, $model);
                echo CJSON::encode(array('success' => 'true',
                                         'msg' => 'Запись успешно отредактирована.'));
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function addEditModelPregnant($pregnant, $model) {
        $pregnant->doctor_id = $model->doctorId;
        $pregnant->register_type = $model->registerType;
        $pregnant->card_id = $model->cardId;

        if(!$pregnant->save()) {
            echo CJSON::encode(array('success' => true,
                                     'error' => 'Произошла ошибка записи данных о беременной.'));
            exit();
        }
    }

    // Поиск беременных
    public function actionSearchPregnant() {
        $model = new FormPregnantSearch();
        if(isset($_POST['FormPregnantSearch'])) {
            $model->attributes = $_POST['FormPregnantSearch'];
            if($model->validate()) {
                $filters = array(
                    'groupOp' => 'AND',
                    'rules' => array(
                        array(
                            'field' => 'first_name',
                            'op' => 'cn',
                            'data' => $model->firstName
                        ),
                        array(
                            'field' => 'last_name',
                            'op' => 'cn',
                            'data' => $model->lastName
                        ),
                        array(
                            'field' => 'middle_name',
                            'op' => 'cn',
                            'data' => $model->middleName
                        ),
                        array(
                            'field' => 'oms_number',
                            'op' => 'cn',
                            'data' => $model->omsNumber
                        ),
                        array(
                            'field' => 'card_number',
                            'op' => 'cn',
                            'data' => $model->cardNumber
                        ),
                        array(
                            'field' => 'gender',
                            'op' => 'eq',
                            'data' => 0
                        )
                    )
                );
                // Проверим, не задан ли пустой поисковой запрос
                $isNotEmpty = false;
                foreach($filters['rules'] as $filter) {
                    if(trim($filter['data']) != '' && $filter['field'] != 'gender') {
                        $isNotEmpty = true;
                        break;
                    }
                }
                $result = $this->searchPatients($isNotEmpty !== false ? CJSON::encode($filters) : false);
                echo CJSON::encode(array('success' => 'true',
                                         'data' => $result));
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    // Сделать поисковую модель
    private function fillAndGetSearchFields() {
        $model = new FormPregnantSearch();
        if(isset($_GET['cardid']) && trim($_GET['cardid']) != '') {
            // Сначала ищем медкарту, а потом по ней - полис.
            $medcard = Medcard::model()->findByPk($_GET['cardid']);
            if($medcard == null) {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => 'Невозможно найти медкарту!'));
                exit();
            }
            $oms = Oms::model()->findByPk($medcard->policy_id);
            if($oms != null && $oms->gender == 0) { // Женский пол
                $model->cardNumber = $medcard->card_number;
                $model->omsNumber = $oms->oms_number;
                $model->firstName = $oms->first_name;
                $model->lastName = $oms->last_name;
                $model->middleName = $oms->middle_name;
                $model->id = $oms->id;
                $model->policyGivedate = $oms->givedate;
                $model->policyEnddate = $oms->enddate;
            }
        }
        return $model;
    }

    // Сделать модель данных о беременной
    private function fillAndGetPregnantFields($cardNumber) {
        $model = new FormPregnantAdd();
        if($cardNumber != null) { // Пациентка была уже выбрана
            // Выбрать по карте все параметры беременности
            $pregnant = Pregnant::model()->find('card_id = :card_id', array(':card_id' => $cardNumber));
            if($pregnant != null) {
                $model->id = $pregnant->id;
                $model->registerType = $pregnant->register_type;
                $model->doctorId = $pregnant->doctor_id;
            }
            $model->cardId = $cardNumber;
        }
        return $model;
    }

    // Экшн записи пациента: шаг 1
    public function actionWritePatientStepOne() {
        $this->render('writePatient1', array(

        ));
    }

    // Экшн записи пациента: шаг 1
    public function actionWritePatientStepTwo() {
        if(isset($_GET['cardid'])) {
            // Проверим, что такая карта реально есть
            $medcard = Medcard::model()->findByPk($_GET['cardid']);
            if($medcard != null) {
                // Список отделений
                $ward = new Ward();
                $wardsResult = $ward->getRows(false);
                $wardsList = array('-1' => 'Нет');
                foreach($wardsResult as $key => $value) {
                    $wardsList[$value['id']] = $value['name'];
                }

                // Список должностей
                $post = new Post();
                $postsResult = $post->getRows(false);
                $postsList = array('-1' => 'Нет');
                foreach($postsResult as $key => $value) {
                    $postsList[$value['id']] = $value['name'];
                }

                $this->render('writePatient2', array(
                    'wardsList' => $wardsList,
                    'postsList' => $postsList,
                    'medcard' => $medcard
                ));
                exit();
            }
        }

        $req = new CHttpRequest();
        $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/writepatientstepone'));
    }
}


?>