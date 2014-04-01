<?php
class PatientController extends Controller {
    public $layout = 'application.modules.reception.views.layouts.index';

    // Стартовая
    public function actionIndex() {
        $this->render('index', array());
    }

    // Просмотр страницы поиска пациента
    public function actionViewSearch() {
        $this->render('searchPatient', array(
            'privilegesList' => $this->getPrivileges(),
            'modelMedcard' => new FormPatientWithCardAdd(),
            'modelOms' => new FormOmsEdit()
        ));
    }

    // Получить страницу просмотра истории движения медкарты
    public function actionViewHistoryMotion()
    {
        if(!isset($_GET['omsid'])) {
            exit('Нехватка данных!');
        }
        $omsId = trim($_GET['omsid']);
        $omsModel = Oms::model()->findByPk($omsId );
        $this->render('historyOfMotion', array(
            'fio' => $omsModel->last_name.' '.$omsModel->first_name.' '.$omsModel->middle_name,
		    'omsid' => $omsId
	    ));
    }

    // Получить саму историю движения медкарты
    public function actionGetHistoryMotion() {
        $rows = $_GET['rows'];
        $page = $_GET['page'];
        $sidx = $_GET['sidx'];
        $sord = $_GET['sord'];

        if(isset($_GET['omsid'])) {
            $omsId = trim($_GET['omsid']);
        }
        if(isset($_GET['medcardid'])) {
            $medcard = Medcard::model()->findByPk($_GET['medcard']);
            if($medcard != null) {
                $omsId = $medcard->policy_id;
            } else {
                exit('Нехватка данных для запроса.');
            }
        }

        $model = new Medcard();
        $num = $model->getHistoryOfMotion($omsId);

        $totalPages = ceil(count($num) / $rows);
        $start = $page * $rows - $rows;

        $history = $model->getHistoryOfMotion($omsId, $sidx, $sord, $start, $rows);

        foreach($history as &$element) {
            $parts = explode(' ', $element['greeting_timestamp']);
            $subparts1 = explode('-', $parts[0]);
            $subparts2 = explode(':', $parts[1]);
            $element['greeting_timestamp'] = $subparts1[2].'.'.$subparts1[1].'.'.$subparts1[0].' '.$subparts2[0].':'.$subparts2[1];
        }

        echo CJSON::encode(
            array('rows' => $history,
                'total' => $totalPages,
                'records' => count($num))
        );
	    
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

        if(isset($_GET['patientid']) && !isset($_GET['mediateid'])) {
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
                    // Ищем привилегии
                    $privileges = PatientPrivilegie::model()->findAll('patient_id = :patient_id', array(':patient_id' => $medcard->policy_id));
                } else {
                    $privileges = array();
                }

                if(count($privileges) > 0) {
                    // TODO: пока говорим о том, что одному пациенту соответствует одна привилегия. В будущем будем писать для целого массива льгот
                    $this->fillPrivilegeFormPart($formModel, $privileges);
                }
                $formModel->mediateId = -1;
                $formModel->policy = $patient->id;

                $this->render('addPatientWithCard', array(
                    'model' => $formModel,
                    'policy_number' => $patient->oms_number,
                    'policy_id' => $patient->id,
                    'fio' => $patient->first_name.' '.$patient->last_name.' '.$patient->middle_name,
                    'regPoint' => date('Y'),
                    'privilegesList' => $privilegesList,
                    'foundPriv' => count($privileges) > 0,
                    'id' => -1
                ));
            } else {
                $model = new FormPatientAdd();
                $this->render('addPatientWithoutCard', array(
                    'model' => $model,
                    'regPoint' => date('Y'),
                    'privilegesList' => $privilegesList,
                    'foundPriv' => false,
                    'policy_number' => -1,
                    'policy_id' => -1,
                ));
            }
        } else {
            // Регистрация опосредованного пациента: сопоставление с сущестующими ОМС
            if(isset($_GET['mediateid'], $_GET['patientid'])) {
                $oms = Oms::model()->findByPk($_GET['patientid']);
                $mediate = MediatePatient::model()->findByPk($_GET['mediateid']);
                $model = new FormPatientWithCardAdd();

                if($oms == null) {
                    $model->policy = -1;
                } else {
                    $model->policy = $oms->id;
                }

                if($mediate == null) {
                    $model->mediateId = -1;
                } else {
                    $model->mediateId = $mediate->id;
                    $model->contact = $mediate->phone;
                }

                $this->render('addPatientWithCard', array(
                    'model' => $model,
                    'regPoint' => date('Y'),
                    'privilegesList' => $privilegesList,
                    'foundPriv' => false,
                    'fio' => $oms->first_name.' '.$oms->last_name.' '.$oms->middle_name,
                    'policy_number' => $oms->oms_number
                ));
                exit();
            }

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
                $this->addEditModelMedcard($medcard, $model, $oms);;
                if($model->privilege != -1) {
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

                $medcard = $this->addEditModelMedcard($medcard, $model, $oms);

                // Карта сделана из опосредованного пациента
                if($model->mediateId != -1) {
                    // Нужно удалить запись о опосредованном пациенте и переписать всех опосредованных пациентов на данную медкарту
                    $sheduleList = SheduleByDay::model()->findAll('t.mediate_id = :mediate_id', array(':mediate_id' => $model->mediateId));
                    foreach($sheduleList as $element) {
                        $element->medcard_id = $medcard->card_number;
                        $element->mediate_id = null;
                        if(!$element->save()) {
                            echo CJSON::encode(array('success' => 'false',
                                                     'data' => 'Не могу перенести запись опосредованного пациента!'));
                            exit();
                        }
                    }
                }

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
        $oms->status = $model->status;
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
        // Это ситуация, когда привилегия создаётся посредством редактирования карты
        if($privilege == null) {
            $privilege = new PatientPrivilegie();
        }
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
        if(!isset($_GET['cardid'])) {
            exit('Нехватка данных для редактирования медкарты!');
        }

        $data = $this->prepareMedcard($_GET['cardid']);

        $this->render('editMedcard', array(
            'model' => $data['formModel'],
            'policy_number' => $data['oms']->oms_number,
            'policy_id' => $data['oms']->id,
            'card_number' => $data['medcard']->card_number,
            'fio' => $data['oms']->first_name.' '.$data['oms']->last_name.' '.$data['oms']->middle_name,
            'privilegesList' => $data['privilegesList'],
            'foundPriv' => count($data['privileges']) > 0
        ));
    }

    // Редактирование карты, простое возвращение данных
    public function actionGetMedcardData() {
        if(!isset($_GET['cardid'])) {
            exit('Нехватка данных для редактирования медкарты!');
        }

        $data = $this->prepareMedcard($_GET['cardid']);
        echo CJSON::encode(array('success' => true,
                                 'data' => $data));
    }

    // Подготовка данных медкарты
    private function prepareMedcard() {
        $modelCard = new Medcard();
        $medcard = $modelCard->findByPk($_GET['cardid']);
        if($medcard == null) {
            $req = new CHttpRequest();
            $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/viewsearch'));
        }
        $modelOms = new Oms();
        $oms = $modelOms->findByPk($medcard->policy_id);
        if($oms == null) {
            exit('Такого пациента не существует!');
        }

        $formModel = new FormPatientWithCardAdd();
        $this->fillFormMedcardModel($formModel, $medcard);

        $privileges = PatientPrivilegie::model()->findAll('patient_id = :patient_id', array(':patient_id' => $oms->id));
        if(count($privileges) > 0) {
            // TODO: пока говорим о том, что одному пациенту соответствует одна привилегия. В будущем будем писать для целого массива льгот
            $this->fillPrivilegeFormPart($formModel, $privileges);
        } else {
            $formModel->privilege = -1;
        }
        $privilegesList = $this->getPrivileges();
        return array(
            'formModel' => $formModel,
            'oms' => $oms,
            'medcard' => $medcard,
            'privilegesList' => $privilegesList,
            'privileges' => $privileges
        );
    }

    // Заполнение модели формы значениями
    private function fillFormMedcardModel($formModel, $medcard) {
        // Заполняем модель
        $formModel->serie = $medcard->serie;
        $formModel->snils = $medcard->snils;
        if(trim($medcard->address) != '') {
            $address = $this->getAddressStr($medcard->address);
            $formModel->addressHidden = $address['addressHidden'];
            $formModel->address = $address['addressStr'];
        }

        if(trim($medcard->address_reg) != '') {
            $address = $this->getAddressStr($medcard->address_reg);
            $formModel->addressRegHidden = $address['addressHidden'];
            $formModel->addressReg = $address['addressStr'];
        }

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

    private function getAddressStr($address) {
        $data = CJSON::decode($address);
        $cladrController = Yii::app()->createController('guides/cladr');
        if(!is_array($data) && !is_object($data)) {
            $data = array();
        }
        $data['returnData'] = 1;
        $address = $cladrController[0]->actionGetCladrData($data);
        $addressStr = '';
        if($address['region'] != null) {
            $addressStr = $address['region'][0]['name'].', ';
        } else {
            $addressStr = 'Регион неизвестен, ';
        }
        if($address['district'] != null) {
            $addressStr .= $address['district'][0]['name'].', ';
        } else {
            $addressStr .= 'район неизвестен, ';
        }
        if($address['settlement'] != null) {
            $addressStr .= $address['settlement'][0]['name'].', ';
        } else {
            $addressStr .= 'населённый пункт неизвестен, ';
        }
        if($address['street'] != null) {
            $addressStr .= $address['street'][0]['name'];
        } else {
            $addressStr .= 'улица неизвестна';
        }
        return array(
            'addressStr' => $addressStr,
            'addressHidden' => CJSON::encode($address)
        );
    }

    private function fillFormMedcardMediateModel($formModel, $oms = false, $mediate = false) {
        if($mediate !== false) {
            $formModel = $this->fillMediateForm($formModel, $mediate);
        }
        if($oms !== false) {
            $formModel->omsId = $oms->id;
        }
        return $formModel;
    }

    private function fillMediateForm($formModel, $mediate) {
        $formModel->contact = $mediate->phone;
        return $formModel;
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

    private function fillOmsModel($formModel, $oms) {
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
        $formModel->status = $oms->status;
        return $formModel;
    }

    // Редактирование полиса (ОМС), вьюха
    public function actionEditOmsView() {
        if(isset($_GET['omsid'])) {
            exit('Нехватка данных: нет номера полиса!');
        }

        $data = $this->prepareOms($_GET['omsid']);

        $this->render('editOms', array(
            'model' => $data['formModel'],
            'policy_number' => $data['oms']->oms_number,
            'policy_id' => $data['oms']->id,
            'fio' => $data['oms']->first_name.' '.$data['oms']->last_name.' '.$data['oms']->middle_name
        ));
    }

    // Редактирование полиса простое возвращение данных
    public function actionGetOmsData() {
        if(!isset($_GET['omsid'])) {
            exit('Нехватка данных для редактирования полиса!');
        }

        $data = $this->prepareOms($_GET['omsid']);
        echo CJSON::encode(array('success' => true,
                                 'data' => $data));
    }

    private function prepareOms() {
        $modelOms = new Oms();
        $oms = $modelOms->findByPk($_GET['omsid']);
        if($oms == null) {
            $req = new CHttpRequest();
            $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/viewsearch'));
        }

        $formModel = new FormOmsEdit();
        $formModel = $this->fillOmsModel($formModel, $oms);
        return array(
          'formModel' => $formModel,
          'oms' => $oms
        );
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
                        $this->addEditModelPrivilege($patientPrivelege[0], $model, $medcard->policy_id);
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
        $medcard->address = $model->addressHidden;
        $medcard->address_reg = $model->addressRegHidden;
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
        $medcard->enterprise_id = 1; // TODO: сделать выборку из учреждений, сейчас ставим мониаг жёстко

        if($oms) {
            $medcard->policy_id = $oms->id;
        }

        if(!$medcard->save()) {
            echo CJSON::encode(array('success' => true,
                                     'error' => 'Произошла ошибка записи новой медкарты.'));
            exit();
        }

        return $medcard;
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
        // Проверим наличие фильтров
        $filters = $this->checkFilters();

        $rows = $_GET['rows'];
        $page = $_GET['page'];
        $sidx = $_GET['sidx'];
        $sord = $_GET['sord'];

        $WithOnly = false;
        $WithoutOnly = false;

        $oms = array();
        if ((isset($_GET['withonly'])) && ($_GET['withonly'] == 0)) {
            $WithOnly = true;
        }

        if ((isset($_GET['withoutonly'])) && ($_GET['withoutonly'] == 0)) {
            $WithoutOnly = true;
        }

        if ((isset($_GET['mediateonly'])) && ($_GET['mediateonly'] == 0)) {
            $mediateOnly = true;
        } else {
            $mediateOnly = false;
        }

        if(!$mediateOnly) {
            $model = new Oms();
            // Вычислим общее количество записей
            $num = $model->getRows($filters,false,false,false,false,$WithOnly,$WithoutOnly);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $items = $model->getRows($filters, $sidx, $sord, $start, $rows, $WithOnly, $WithoutOnly);

            // Обрабатываем результат
            foreach($items as $index => &$item) {
                if($item['reg_date'] != null) {
                    $parts = explode('-', $item['reg_date']);
                    $item['reg_date'] = $parts[0];
                }

                if($item['birthday'] != null) {
                    $parts = explode('-', $item['birthday']);
                    $item['birthday'] = $parts[2].'.'.$parts[1].'.'.$parts[0];
                }
            }
        } else {
            // Забираем фильтры только те, которые нужны: ФИО
            $filters['rules'] = array_filter($filters['rules'], function(&$filter) {
                return array_search($filter['field'], array('first_name', 'last_name', 'middle_name')) !== false;
            });
            // Теерь проверяем фильтры
            $numEmpty = 0;
            foreach($filters['rules'] as $filter) {
                if(array_search($filter['field'], array('first_name', 'last_name', 'middle_name')) !== false && (!isset($filter['data']) || trim($filter['data']) == '')) {
                    $numEmpty++;
                }
            }
            if($numEmpty == 3) {
                $num = 0;
                $totalPages = 0;
                $items = array();
            } else {
                $model = new MediatePatient();
                $num = $model->getRows($filters, false, false, false);
                $totalPages = ceil(count($num) / $rows);
                $start = $page * $rows - $rows;
                $items = $model->getRows($filters, $sidx, $sord, $start, $rows);
            }
        }
        echo CJSON::encode(
           array(
                'success' => true,
                'rows' => $items,
                'total' => $totalPages,
                'records' => count($num)
             )
        );

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

        foreach($filters['rules'] as &$filter) {
            if(isset($filter['data']) && trim($filter['data']) != '') {
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
    
    private function searchPatients($filters = false, $distinct = false) {
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
        if(!$distinct) {
            $oms = $model->getRows($filters);
        } else {
            $oms = $model->getDistinctRows($filters);
        }
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
            'privilegesList' => $this->getPrivileges(),
            'modelMedcard' => new FormPatientWithCardAdd(),
            'modelOms' => new FormOmsEdit()
        ));
    }

    // Экшн записи пациента: шаг 1
    public function actionWritePatientStepTwo() {
        if(isset($_GET['cardid'])) {
            // Проверим, что такая карта реально есть
            $medcard = Medcard::model()->findByPk($_GET['cardid']);
            if($medcard != null) {
                $this->render('writePatient2', array(
                    'wardsList' => $this->getWardsList(),
                    'postsList' => $this->getPostsList(),
                    'medcard' => $medcard
                ));
                exit();
            }
        }

        $req = new CHttpRequest();
        $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/reception/patient/writepatientstepone'));
    }

    private function getWardsList() {
        // Список отделений
        $ward = new Ward();
        $wardsResult = $ward->getRows(false);
        $wardsList = array('-1' => 'Нет');
        foreach($wardsResult as $key => $value) {
            $wardsList[$value['id']] = $value['name'];
        }
        return $wardsList;
    }

    private function getPostsList() {
        // Список должностей
        $post = new Post();
        $postsResult = $post->getRows(false);
        $postsList = array('-1' => 'Нет');
        foreach($postsResult as $key => $value) {
            $postsList[$value['id']] = $value['name'];
        }
        return $postsList;
    }

    // Запись опосредованного пациента
    public function actionWritePatientWithoutData() {
        $this->render('writepatientwithoutdata', array(
            'wardsList' => $this->getWardsList(),
            'postsList' => $this->getPostsList(),
        ));
    }

    public function actionMediateToMedcard() {
        if(!isset($_GET['medcardid'], $_GET['mediateid'])) {
            echo CJSON::encode(array('success' => 'false',
                                     'data' => 'Нехватка данных!'));
            exit();
        }
        $medcard = Medcard::model()->findByPk($_GET['medcardid']);
        if($medcard == null) {
            echo CJSON::encode(array('success' => 'false',
                                     'data' => 'Нехватка данных!'));
            exit();
        }

        $sheduleList = SheduleByDay::model()->findAll('t.mediate_id = :mediate_id', array(':mediate_id' => $_GET['mediateid']));
        foreach($sheduleList as $element) {
            $element->medcard_id = $medcard->card_number;
            $element->mediate_id = null;
            if(!$element->save()) {
                echo CJSON::encode(array('success' => 'false',
                                         'data' => 'Не могу перенести запись опосредованного пациента!'));
                exit();
            }
        }
        MediatePatient::model()->deleteByPk($_GET['mediateid']);
        // TODO: дальше нужно переадресовать на экшн записи таким образом, чтобы автоматом раскрыть расписание к этому пациенту
        echo CJSON::encode(array('success' => 'true',
                                 'data' => 'Пациент успешно сопоставлен и записан на приём!'));
    }


    public function actionChangeMedcardStatus($ids, $status) {
        $ids = CJSON::decode($ids);
        if(count($ids) == 0) {
            echo CJSON::encode(array('success' => false,
                                     'error' => 'Не хватает данных!'));
        }

        foreach($ids as $medcardId) {
            $medcard = Medcard::model()->findByPk($medcardId);
            if($medcard != null) {
                $medcard->motion = $status;
                if(!$medcard->save()) {
                    echo CJSON::encode(array('success' => false,
                                             'error' => 'Не могу сохранить изменение статуса медкарты в базе!'));
                }
            }
        }

        echo CJSON::encode(array('success' => true,
                                 'data' => 'Статус карт успешно изменён.'));
    }
}


?>