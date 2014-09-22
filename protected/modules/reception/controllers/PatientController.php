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

    // Привязать карту к другому полису
    public function actionRebindOmsMedcard()
    {
        $cardNumber = '';
        $newPolicyId = 0;

        if (isset($_GET['cardNumber']) && isset($_GET['newOmsId']))
        {
            $cardNumber = $_GET['cardNumber'];
            $newPolicyId = $_GET['newOmsId'];

            // Сначала проверим - есть ли у полиса, на который мы перекидываем карта с тем же годом. если она есть - то
            //   перекидывать нельзя, нужно использовать ту карту

            $yearOfCard = substr($cardNumber, count($cardNumber)-3);

           // var_dump($yearOfCard );
         //   exit();

            // Найдём карту с номером полиса, равным тому, на который мы перекидываем и с тем же годом
            $oldMedcards = Medcard::model()->find("policy_id = :oms AND card_number like '%".$yearOfCard."'",
                array(':oms' => $newPolicyId)
            );

            if ($oldMedcards!=null)
            {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => array (
                                             'Для данного полиса уже есть карта данного года.'

                                         )
                ));
                exit();
            }

            // 1. Выполняем перепривязку медкарты
            $cardToRebind = Medcard::model()->find('card_number = :card', array ( ':card' =>  $cardNumber ) );
            $oldPolicyId = $cardToRebind['policy_id'];
            $cardToRebind['policy_id'] = $newPolicyId;
            $cardToRebind->save();

            // 2. Пишем в таблицу rebinded_cards о смене полиса у карты
            $rebindLog = new RebindedMedcard();
            $rebindLog->card_number = $cardNumber;
            $rebindLog->old_policy = $oldPolicyId;
            $rebindLog->new_policy = $newPolicyId;
            $rebindLog->changing_timestamp =  date('Y-m-d H:i:s');
            $userToWrite = User::model()->findByAttributes(array('id' => Yii::app()->user->id));
            $rebindLog->worker_id = $userToWrite['employee_id'];
            $rebindLog->save();
            echo CJSON::encode(array('success' => 'true'));
        }
        else
        {
            echo CJSON::encode(array('success' => 'false'));
        }

    }
	
	public function actionGenerateCardNumber() {
		if(!isset($_GET['ruleid'])) {
			echo CJSON::encode(array('success' => false));
			exit();
		}
		$generator = new CardnumberGenerator();
		$cardNumber = $generator->generateNumber($_GET['ruleid']);
	}
	
	// Удалить ОМС
	public function actionDeleteOms() {
		if(!isset($_GET['omsid'])) {
			echo CJSON::encode(array('success' => false));
			exit();
		}
		Oms::model()->deleteByPk($_GET['omsid']);
		echo CJSON::encode(array('success' => true));
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

    public function actionViewRewrite()
    {
        $this->render('rewriting', array());


    }


    public function actionGetPatientsToRewrite() {
        $sheduleElements = array();
        $mediateElements = array();

        if(isset($_GET['status'])) {
            $mediateOnly = $_GET['status'];
        } else {
            $mediateOnly = 0;
        }

        $filters = array(
            'groupOp' => 'AND',
            'rules' => array()
        );

        // Далее проверяем - если есть фильтрующие поля, то записываем их в фильтры
        if (isset ( $_GET['date']) && $_GET['date']!='')
        {
            array_push($filters['rules'],
                array(
                    'field' => 'patient_day',
                    'op' => 'eq',
                    'data' => $_GET['date']
                )
            );
        }
        if($_GET['forDoctors'] == 1)
        {
            $dataD = CJSON::decode($_GET['doctors']);
            array_push($filters['rules'],
            array(
                'field' => 'doctors_ids',
                'op' => 'in',
                'data' => $dataD
            ));
        }
        if ($_GET['forPatients'] == 1)
        {
            $dataP = CJSON::decode($_GET['patients']);
            $dataM = CJSON::decode($_GET['mediates']);
            array_push($filters['rules'],
                array(
                    'field' => 'patients_ids',
                    'op' => 'in',
                    'data' => $dataP
                ));
            array_push($filters['rules'],
                array(
                    'field' => 'mediates_ids',
                    'op' => 'in',
                    'data' => $dataM
                ));


        }
        $sheduleElements = CancelledGreeting::model()->getRows($filters, false, false,false,false, $mediateOnly);
        $num = count($sheduleElements);
        // Разбираем расписание на живую очередь и обычное раписание
        $sheduleElementsWaitingLine = array();
        $sheduleElementsWriting = array();
        foreach($sheduleElements as $key => $element) {
            if($element['order_number'] != null) {
                $sheduleElementsWaitingLine[] = $element;
            } else {
                $sheduleElementsWriting[] = $element;
            }
        }
        if($num > 0) {
            // Первая сортировка идёт всегда по врачу
            $sheduleElementsWaitingLine = SheduleByDay::sortSheduleElements($sheduleElementsWaitingLine);
            $sheduleElementsWriting = SheduleByDay::sortSheduleElements($sheduleElementsWriting);

            // Вторая сортировка - по времени
            $sheduleElementsWaitingLine = SheduleByDay::makeClusters($sheduleElementsWaitingLine);
            $sheduleElementsWriting = SheduleByDay::makeClusters($sheduleElementsWriting);
        }
        // Теперь выясняем кабинет для каждого пациента. Для этого смотрим дату, смотрим расписание врача
        $cabinets = array();
        foreach($sheduleElements as $element) {
            if(!isset($cabinets[$element['doctor_id']])) {
                $weekday = date('w', strtotime($_GET['date']));
                $cabinetElement = SheduleSetted::model()->getCabinetPerWeekday($weekday, $element['doctor_id']);
                if($cabinetElement != null) {
                    $cabinets[$element['doctor_id']] = array('cabNumber' => $cabinetElement['cab_number'],
                        'description' => $cabinetElement['description']);
                } else {
                    $cabinets[$element['doctor_id']] = null;
                }
            }
        }
        echo CJSON::encode(array('success' => true,
            'data' => array('greetings' => $sheduleElements,
                'cabinets' => $cabinets,
                'greetingsOnlyByWriting' => $sheduleElementsWriting,
                'greetingsOnlyWaitingLine' => $sheduleElementsWaitingLine)));



        //var_dump($filters);
        //exit();

        // Если есть дата - записываем её в правила
/*



        if($_GET['forDoctors'] == 1 && $_GET['forPatients'] == 1) {
            $dataD = CJSON::decode($_GET['doctors']);
            $dataP = CJSON::decode($_GET['patients']);
            $dataM = CJSON::decode($_GET['mediates']);
            $filters = array(
                'groupOp' => 'AND',
                'rules' => array(
                    array(
                        'field' => 'doctors_ids',
                        'op' => 'in',
                        'data' => $dataD
                    ),
                    array(
                        'field' => 'patients_ids',
                        'op' => 'in',
                        'data' => $dataP
                    ),
                    array(
                        'field' => 'mediates_ids',
                        'op' => 'in',
                        'data' => $dataM
                    ),
                    array(
                        'field' => 'patient_day',
                        'op' => 'eq',
                        'data' => $_GET['date']
                    )
                )
            );


            $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit($filters, false, false, $mediateOnly);
        } elseif($_GET['forDoctors'] == 1 && $_GET['forPatients'] == 0) {
            $data = CJSON::decode($_GET['doctors']);
            $filters = array(
                'groupOp' => 'AND',
                'rules' => array(
                    array(
                        'field' => 'doctors_ids',
                        'op' => 'in',
                        'data' => $data
                    ),
                    array(
                        'field' => 'patient_day',
                        'op' => 'eq',
                        'data' => $_GET['date']
                    )
                )
            );

            $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit($filters, false, false, $mediateOnly);
        } elseif($_GET['forDoctors'] == 0 && $_GET['forPatients'] == 1) {
            $data = CJSON::decode($_GET['patients']);
            $dataM = CJSON::decode($_GET['mediates']);
            $filters = array(
                'groupOp' => 'AND',
                'rules' => array(
                    array(
                        'field' => 'patients_ids',
                        'op' => 'in',
                        'data' => $data
                    ),
                    array(
                        'field' => 'mediates_ids',
                        'op' => 'in',
                        'data' => $dataM
                    ),
                    array(
                        'field' => 'patient_day',
                        'op' => 'eq',
                        'data' => $_GET['date']
                    )
                )
            );
            $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit($filters, false, false, $mediateOnly);
        } else {
            $filters = array(
                'groupOp' => 'AND',
                'rules' => array(
                    array(
                        'field' => 'patient_day',
                        'op' => 'eq',
                        'data' => $_GET['date']
                    )
                )
            );

            $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit($filters, false, false, $mediateOnly);
        }

        $result = array();
        $num = count($sheduleElements);
        if(isset($mediateElements)) {
            foreach($mediateElements as $element) {
                array_push($sheduleElements, $element);
            }
        }

        $num = count($sheduleElements);
        // Разбираем расписание на живую очередь и обычное раписание
        $sheduleElementsWaitingLine = array();
        $sheduleElementsWriting = array();
        foreach($sheduleElements as $key => $element) {
            if($element['order_number'] != null) {
                $sheduleElementsWaitingLine[] = $element;
            } else {
                $sheduleElementsWriting[] = $element;
            }
        }
        if($num > 0) {
            // Первая сортировка идёт всегда по врачу
            $sheduleElementsWaitingLine = $this->sortSheduleElements($sheduleElementsWaitingLine);
            $sheduleElementsWriting = $this->sortSheduleElements($sheduleElementsWriting);

            // Вторая сортировка - по времени
            $sheduleElementsWaitingLine = $this->makeClusters($sheduleElementsWaitingLine);
            $sheduleElementsWriting = $this->makeClusters($sheduleElementsWriting);
        }
        // Теперь выясняем кабинет для каждого пациента. Для этого смотрим дату, смотрим расписание врача
        $cabinets = array();
        foreach($sheduleElements as $element) {
            if(!isset($cabinets[$element['doctor_id']])) {
                $weekday = date('w', strtotime($_GET['date']));
                $cabinetElement = SheduleSetted::model()->getCabinetPerWeekday($weekday, $element['doctor_id']);
                if($cabinetElement != null) {
                    $cabinets[$element['doctor_id']] = array('cabNumber' => $cabinetElement['cab_number'],
                        'description' => $cabinetElement['description']);
                } else {
                    $cabinets[$element['doctor_id']] = null;
                }
            }
        }

        echo CJSON::encode(array('success' => true,
            'data' => array('shedule' => $sheduleElements,
                'cabinets' => $cabinets,
                'sheduleOnlyByWriting' => $sheduleElementsWriting,
                'sheduleOnlyWaitingLine' => $sheduleElementsWaitingLine)));*/
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
            if($element['greeting_timestamp'] != null) {
                $parts = explode(' ', $element['greeting_timestamp']);
                $subparts1 = explode('-', $parts[0]);
                $subparts2 = explode(':', $parts[1]);
                $element['greeting_timestamp'] = $subparts1[2].'.'.$subparts1[1].'.'.$subparts1[0].' '.$subparts2[0].':'.$subparts2[1];
            } else {
                $element['greeting_timestamp'] = '';
            }
        }

        echo CJSON::encode(
            array('rows' => $history,
                'total' => $totalPages,
                'records' => count($num))
        );
	    
    }

    public function actionGetIsOmsWithNumber()
    {
       // var_dump($_GET['omsIdToCheck']);
       // exit();

        $newOms = null;
        $oldOms = null;
        $result = array();
        if (isset($_GET['omsNumberToCheck'])&& isset($_GET['omsIdToCheck']))
        {
            //var_dump($_GET['omsIdToCheck']);
            //exit();
            if ( isset ($_GET['omsSeriesToCheck']) && $_GET['omsSeriesToCheck']!='' )
            {
                $newOms= $this->checkUnickueOmsInternal($_GET['omsSeriesToCheck'].' '.$_GET['omsNumberToCheck'],
                    $_GET['omsIdToCheck'],true);
            }
            else
            {
                $newOms= $this->checkUnickueOmsInternal($_GET['omsNumberToCheck'],$_GET['omsIdToCheck'],true);
            }
            // Если омс!=нуль, то значит, что полис с таким номером существует в базе
            if ($newOms!=null)
            {
                // Вытащим ОМС по ИД и сравним: если ФИО и дата рождения не совпадает - выводим флаг, который скажет,
                //     нужно вывести сообщение, чтобы оператор проверил все данные

                $oldOms = Oms::model()->findByPk( $_GET['omsIdToCheck'] );

                $result['oldOms'] = $oldOms;
                $result['newOms'] = $newOms;

                // Сравним данные по $oms и $oldOms
                if (
                    (mb_strtolower($newOms['first_name'], 'UTF-8') != mb_strtolower($oldOms['first_name'], 'UTF-8'))||
                    (mb_strtolower($newOms['last_name'], 'UTF-8') != mb_strtolower($oldOms['last_name'], 'UTF-8'))||
                    (mb_strtolower($newOms['middle_name'], 'UTF-8') != mb_strtolower($oldOms['middle_name'], 'UTF-8'))||
                    (mb_strtolower($newOms['birthday'], 'UTF-8') != mb_strtolower($oldOms['birthday'], 'UTF-8'))

                )
                {
                    // Совпадения нет
                    $result['nonCoincides'] = true;
                }

                // вытащим номер карты, у которой максимален номер по данному полису
                $medcardObject = new Medcard();
                $lastMedcardNewOms = $medcardObject->getLastByPatient(  $newOms['id']  );

                // Вытащим номер карты по старому полису (т.е. медкарты,
                //    которая в настоящий момент привязана к полису, номер которого меняется)
                $lastMedcardOldOms = $medcardObject->getLastByPatient(  $_GET['omsIdToCheck']  );

                // Записываем номера медкарт для нового и для старого полиса
                if ($lastMedcardOldOms!=null && count($lastMedcardOldOms)!=0)
                {
                    $result['oldMedcard'] = $lastMedcardOldOms ['card_number'] ;
                }

                if ($lastMedcardNewOms!=null && count($lastMedcardNewOms)!=0)
                {
                    $result['newMedcard'] = $lastMedcardNewOms ['card_number'] ;
                }
            }
        }

        echo CJSON::encode(
            array('answer' => $result)
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
				// Если нет региона и страховой компании, то подгрузить их
				$tasuStatus = true;
				try {
					$tasuController = Yii::app()->createController('admin/tasu');
					$result = $tasuController[0]->getTasuPatientByPolicy($patient);
					/*if($result === -1) {
						$tasuStatus = false;
					}*/
				} catch(Exception $e) {
					$tasuStatus = false;
				}

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
                    'fio' => $patient->last_name.' '.$patient->first_name.' '.$patient->middle_name,
                    'regPoint' => date('Y'),
                    'privilegesList' => $privilegesList,
                    'foundPriv' => count($privileges) > 0,
                    'id' => -1,
                    'actionAdd' => 'addcard',
					'tasuStatus' => $tasuStatus
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
                    'actionAdd' => 'add'
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
                    'fio' => $oms->last_name.' '.$oms->first_name.' '.$oms->middle_name,
                    'policy_number' => $oms->oms_number,
                    'actionAdd' => 'addcard'
                ));
                exit();
            }
            $model = new FormPatientAdd();
            $this->render('addPatientWithoutCard', array(
                'model' => $model,
                'regPoint' => date('Y'),
                'privilegesList' => $privilegesList,
                'foundPriv' => false,
                'actioadd' => 'add'
            ));
        }
    }

    // Добавление пациента
    public function actionAdd() {
        $model = new FormPatientAdd();
        if(isset($_POST['FormPatientAdd'])) {
            $model->attributes = $_POST['FormPatientAdd'];
            $model->insurance = $_POST['FormPatientAdd']['insurance'];
            $model->region = $_POST['FormPatientAdd']['region'];
            // Если телефон равен +7, значит его не ввели
            if ($model->contact=="+7")
                $model->contact = "";
            if($model->validate()) {
                $medcard = new Medcard();
               // var_dump('!');
                $oms = $this->checkUniqueOms($model);
               // var_dump('?');
               // var_dump($oms);
               // exit();
                // Если пациент с таким полисом найден, просто создаётся карта и подсоединяется полис
                if($oms == null) {
                    //var_dump('!');
                    //exit();
                    $oms = new Oms();
                    $this->addEditModelOms($oms, $model);
                } else {
                    // Здесь нужно проверить: вдруг для пациента есть уже медкарта в этом году, для такого полиса
                    $this->checkIssetMedcardInYear($oms, $medcard);
                }
                $this->checkUniqueMedcard($model);
                $this->addEditModelMedcard($medcard, $model, $oms);;
                if($model->privilege != -1) {
                    $patientPrivelege = new PatientPrivilegie();
                    $this->addEditModelPrivilege($patientPrivelege, $model, $oms->id);
                }
				
				if(is_array($oms)) {
					$fioBirthdayStr = $oms['last_name'].' '.$oms['first_name'].' '.$oms['middle_name'];
					if ($oms['oms_number'] !='') 
					{
						$fioBirthdayStr .=(', номер полиса: '.$oms['oms_number']);
					}
				} else {
					$fioBirthdayStr = $oms->last_name.' '.$oms->first_name.' '.$oms->middle_name;
					if ($oms->oms_number!='')
					{
						$fioBirthdayStr .=(', номер полиса: '.$oms->oms_number);
					}
				}
                echo CJSON::encode(array('success' => 'true',
                                         'msg' => 'Новая запись успешно добавлена!',
                                         'cardNumber' => $medcard->card_number,
                                         'fioBirthday' => $fioBirthdayStr));
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

    private function checkUniqueOms($model, $withoutCurrent = false) {

        $IdOfOms = null;
        if (isset($model->id))
        {
            $IdOfOms = $model->id;
        }
        // Если в моделе есть поле $omsSeries - надо проверить номер вместе с ним
        //  причём в двух вариантах - с прбелом и без
        $seriesSubstringWithSpace = '';
        $seriesSubstringWOSpace = '';
        if (isset($model->omsSeries))
        {
            //$seriesSubstringWOSpace = $model->omsSeries;
            $seriesSubstringWithSpace = $model->omsSeries. ' ';
        }

        /*
        $comarisonResult = $this->checkUnickueOmsInternal($seriesSubstringWithSpace.$model->policy,$IdOfOms,$withoutCurrent);
        if ($comarisonResult===true)
            return $comarisonResult;


        return $this->checkUnickueOmsInternal($seriesSubstringWOSpace.$model->policy,$IdOfOms,$withoutCurrent);
        */
        return $this->checkUnickueOmsInternal($seriesSubstringWithSpace.$model->policy,$IdOfOms,$withoutCurrent);
    }


    private function checkUnickueOmsInternal($omsNumber,$omsId,$withoutCurrent)
    {
        // Если номер ОМС - пустая строка - то возвращаем сразу нуль
        if (  str_replace(array (' ','-'),'',$omsNumber)   == '')
        {
            return null;
        }

        // Старый код. Возможно потом понадобится
        /*
        // Проверим, не существует ли уже такого ОМС
        // Три вида ОМС: с пробелом впереди, с пробелом посередине
        if(mb_strlen($omsNumber) != 16 && !$withoutCurrent) {
            $omsSearched = Oms::model()->find('oms_number = :oms_number', array(':oms_number' => $omsNumber));
        } else {
            $omsNumber1 = $omsNumber;
            $omsNumber2 = ' '.$omsNumber;
            $omsNumber3 = mb_substr($omsNumber, 0, 6).' '.mb_substr($omsNumber, 6);
            if(!$withoutCurrent) {
                $omsSearched = Oms::model()->find(
                    'oms_number = :oms_number1 OR
                    oms_number = :oms_number2 OR
                    oms_number = :oms_number3',
                    array(
                        ':oms_number1' => $omsNumber1,
                        ':oms_number2' => $omsNumber2,
                        ':oms_number3' => $omsNumber3
                    )
                );
            } else {
                if($omsId != null) {
                    $omsSearched = Oms::model()->find(
                        '(oms_number = :oms_number1 OR
                        oms_number = :oms_number2 OR
                        oms_number = :oms_number3)
                        AND id != :policy_id',
                        array(
                            ':oms_number1' => $omsNumber1,
                            ':oms_number2' => $omsNumber2,
                            ':oms_number3' => $omsNumber3,
                            ':policy_id' => $omsId
                        )
                    );
                } else {
                    $omsSearched = Oms::model()->find(
                        'oms_number = :oms_number1 OR
                        oms_number = :oms_number2 OR
                        oms_number = :oms_number3',
                        array(
                            ':oms_number1' => $omsNumber1,
                            ':oms_number2' => $omsNumber2,
                            ':oms_number3' => $omsNumber3
                        )
                    );
                }
            }
        }
        */
        /*
        $omsNumber1 = $omsNumber;
        $omsNumber2 = ' '.$omsNumber;
        $omsNumber3 = mb_substr($omsNumber, 0, 6).' '.mb_substr($omsNumber, 6);
        // Для поиска по нормализованному номеру
        $omsNumberNormalized =  str_replace(array('-',' '), '', $omsNumber);
        //var_dump($withoutCurrent);
        //exit();
        if(!$withoutCurrent) {
            $omsSearched = Oms::model()->find(
                'oms_number = :oms_number1 OR
                oms_number = :oms_number2 OR
                oms_number = :oms_number3 OR
                oms_series_number = :oms_norm_number
                ',
                array(
                    ':oms_number1' => $omsNumber1,
                    ':oms_number2' => $omsNumber2,
                    ':oms_number3' => $omsNumber3,
                    ':oms_norm_number' => $omsNumberNormalized
                )
            );
        } else {
            //var_dump($omsId);
            //exit();

            if($omsId != null) {

                $omsSearched = Oms::model()->find(
                    '(oms_number = :oms_number1 OR
                    oms_number = :oms_number2 OR
                    oms_number = :oms_number3 OR
                    oms_series_number = :oms_norm_number)
                    AND id != :policy_id',
                    array(
                        ':oms_number1' => $omsNumber1,
                        ':oms_number2' => $omsNumber2,
                        ':oms_number3' => $omsNumber3,
                        ':oms_norm_number' => $omsNumberNormalized,
                        ':policy_id' => $omsId
                    )
                );
                //var_dump($omsSearched);
                //exit();

            } else {
                $omsSearched = Oms::model()->find(
                    'oms_number = :oms_number1 OR
                    oms_number = :oms_number2 OR
                    oms_number = :oms_number3 OR
                    oms_series_number = :oms_norm_number',
                    array(
                        ':oms_number1' => $omsNumber1,
                        ':oms_number2' => $omsNumber2,
                        ':oms_number3' => $omsNumber3,
                        ':oms_norm_number' => $omsNumberNormalized
                    )
                );
            }
        }


         // var_dump($omsSearched);
        //  exit();


        if($omsSearched != null) {
            return $omsSearched;
        }
        return null;

        */

        $omsSearched = null;
        $omsNumber1 = $omsNumber;
        $omsNumber2 = ' '.$omsNumber;
        $omsNumber3 = mb_substr($omsNumber, 0, 6).' '.mb_substr($omsNumber, 6);
        // Для поиска по нормализованному номеру
        $omsNumberNormalized =  str_replace(array('-',' '), '', $omsNumber);
        //var_dump($withoutCurrent);
        //exit();
        if(!$withoutCurrent) {
            /*$omsSearched = Oms::model()->find(
                'oms_number = :oms_number1 OR
                oms_number = :oms_number2 OR
                oms_number = :oms_number3 OR
                oms_series_number = :oms_norm_number
                ',
                array(
                    ':oms_number1' => $omsNumber1,
                    ':oms_number2' => $omsNumber2,
                    ':oms_number3' => $omsNumber3,
                    ':oms_norm_number' => $omsNumberNormalized
                )
            );*/
            $omsSearched = Oms::findOmsByNumbers($omsNumber1,$omsNumber2,$omsNumber3,$omsNumberNormalized);

        } else {
            //var_dump($omsId);
            //exit();

            if($omsId != null) {

               /* $omsSearched = Oms::model()->find(
                    '(oms_number = :oms_number1 OR
                    oms_number = :oms_number2 OR
                    oms_number = :oms_number3 OR
                    oms_series_number = :oms_norm_number)
                    AND id != :policy_id',
                    array(
                        ':oms_number1' => $omsNumber1,
                        ':oms_number2' => $omsNumber2,
                        ':oms_number3' => $omsNumber3,
                        ':oms_norm_number' => $omsNumberNormalized,
                        ':policy_id' => $omsId
                    )
                );
                //var_dump($omsSearched);
                //exit();
                */

                $omsSearched = Oms::findOmsByNumbers($omsNumber1,$omsNumber2,$omsNumber3,$omsNumberNormalized,$omsId);
            } else {
              /*  $omsSearched = Oms::model()->find(
                    'oms_number = :oms_number1 OR
                    oms_number = :oms_number2 OR
                    oms_number = :oms_number3 OR
                    oms_series_number = :oms_norm_number',
                    array(
                        ':oms_number1' => $omsNumber1,
                        ':oms_number2' => $omsNumber2,
                        ':oms_number3' => $omsNumber3,
                        ':oms_norm_number' => $omsNumberNormalized
                    )
                );*/
                $omsSearched = Oms::findOmsByNumbers($omsNumber1,$omsNumber2,$omsNumber3,$omsNumberNormalized);
            }
        }


        // var_dump($omsSearched);
        //  exit();


        if($omsSearched != null) {
            return $omsSearched;
        }
        return null;

    }

    // Добавление карты к существующему пациенту
    public function actionAddCard() {
        $model = new FormPatientWithCardAdd();
        if(isset($_POST['FormPatientWithCardAdd'])) {
            $model->attributes = $_POST['FormPatientWithCardAdd'];
            // Проверим - если поле "contact" равно "+7", то занулим его
            if ($model->contact == "+7")
                $model->contact = "";
            if($model->validate()) {
                $oms = Oms::model()->findByPk($model->policy);
                // Проверим, нет ли карты с таким годом и с таким пациентом
                $medcard = new Medcard();
                $this->checkIssetMedcardInYear($oms, $medcard);
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
                                         'msg' => 'Новая запись успешно добавлена!',
                                         'cardNumber' => $medcard->card_number));
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    private function checkIssetMedcardInYear($oms, $medcard) {
        $year = date('Y');
        $code = substr($year, mb_strlen($year) - 2);
	
		if(is_array($oms)) {
			$id = $oms['id'];
		} else {
			$id = $oms->id;
		}
        $medcardSearched = $medcard->getLastMedcardPerYear($code, $id);
        if($medcardSearched != null) {
            echo CJSON::encode(array('success' => 'false',
                'errors' => array(
                    'id' => array(
                        'Карта для данного пациента в этом году уже создана!'
                    )
                )));
            exit();
        }
    }

    // Добавление полиса
    private function addEditModelOms($oms, $model) {
        $oms->first_name = $model->firstName;
        $oms->last_name = $model->lastName;
        // Если c с клиента было подано значение типа омс, то не считываем его из модели
        if ($model->omsType!=-1)
        {
            $oms->type = $model->omsType;
        }
        // Иначе не меняем значение поля "тип" в моделе

        $oms->middle_name = $model->middleName;
        $oms->oms_number = $model->policy;
        $oms->gender = $model->gender;
        $oms->birthday = $model->birthday;
        $oms->givedate = $model->policyGivedate;
        $oms->status = $model->status;
        $oms->insurance = $model->insurance;
        $oms->region = $model->region;
        $oms->oms_series= $model->omsSeries;
        // Добавляем поле oms_series_number
        $seriesNumber = $model->omsSeries . $model->policy;
        // Убиваем пробелы, дефисы из seriesNumber
        $seriesNumber = str_replace(array(' ', '-'), '',  $seriesNumber);
        $oms->oms_series_number = $seriesNumber;

        // Это скорее всего не надо будет
        // Если у полиса тип постоянный - надо вставить пробел между 6-ым и 7-ым символом
        /*if ($oms->type == 5)
        {
            $oms->oms_number = substr($oms->oms_number,0,6).' '.substr($oms->oms_number,6,10);
        }*/

        if(trim($model->policyEnddate) != '') {
            $oms->enddate = $model->policyEnddate;
        }

        //var_dump();

        // Надо перевести ФИО в верхний регистр
        $oms->first_name = mb_strtoupper($oms->first_name, 'utf-8');
        $oms->last_name = mb_strtoupper($oms->last_name, 'utf-8');
        $oms->middle_name = mb_strtoupper($oms->middle_name, 'utf-8');
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
            $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/reception/patient/viewsearch'));
        }
        $modelOms = new Oms();
        $oms = $modelOms->findByPk($medcard->policy_id);
	    if($oms == null) {
            exit('Такого пациента не существует!');
        }
		// Подгрузка из ТАСУ
		$tasuStatus = true;
		if($oms->region == null || $oms->insurance == null) {
			try {
				$tasuController = Yii::app()->createController('admin/tasu');
				$result = $tasuController[0]->getTasuPatientByPolicy($oms);
				/*if($result === -1) {
					$tasuStatus = false;
				}*/
			} catch(Exception $e) {
				$tasuStatus = false;
			}
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
            'privileges' => $privileges,
			'tasuStatus' => $tasuStatus
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
       // $formModel->whoGived = $medcard->who_gived;
       // $formModel->documentGivedate = $medcard->gived_date;
        $formModel->invalidGroup = $medcard->invalid_group;
        $formModel->workPlace = $medcard->work_place;
        $formModel->workAddress = $medcard->work_address;
        $formModel->post = $medcard->post;
        $formModel->contact = $medcard->contact;
        $formModel->cardNumber = $medcard->card_number;
        $formModel->profession = $medcard->profession;
    }

    public function getAddressStr($address, $showEmpty = false) {
        $data = CJSON::decode($address);
        $cladrController = Yii::app()->createController('guides/cladr');
        if(!is_array($data) && !is_object($data)) {
            $data = array();
        }
        $data['returnData'] = 1;
        $address = $cladrController[0]->actionGetCladrData($data);
        $addressStr = '';
        $addressHidden = array();
        if(isset($address['region']) && $address['region'] != null && $address['region'] != '') {
            $addressStr = $address['region'][0]['name'].', ';
            $addressHidden['regionId'] = $address['region'][0]['id'];
        } else {
            if(!$showEmpty) {
                $addressStr = '';
            }
            $addressHidden['regionId'] = null;
        }
        if(isset($address['district']) && $address['district'] != null && $address['district'] != '') {
            $addressStr .= $address['district'][0]['name'].', ';
            $addressHidden['districtId'] =  $address['district'][0]['id'];
        } else {
            if(!$showEmpty) {
                $addressStr .= '';
            }
            $addressHidden['districtId'] = null;
        }
        if(isset($address['settlement']) && $address['settlement'] != null && $address['settlement'] != '') {
            $addressStr .= $address['settlement'][0]['name'].', ';
            $addressHidden['settlementId'] = $address['settlement'][0]['id'];
        } else {
            if(!$showEmpty) {
                $addressStr .= '';
            }
            $addressHidden['settlementId'] = null;
        }
        if(isset($address['street']) && $address['street'] != null && $address['street'] != '') {
            $addressStr .= $address['street'][0]['name'].', ';
            $addressHidden['streetId'] = $address['street'][0]['id'];
        } else {
            if(!$showEmpty) {
                $addressStr .= '';
            }
            $addressHidden['streetId'] = null;
        }

        if(isset($address['house']) && trim($address['house']) != '') {
            $addressStr .= $address['house'].', ';
            $addressHidden['house'] = $address['house'];
        } else {
            if(!$showEmpty) {
                $addressStr .= '';
            }
            $addressHidden['house'] = '';
        }

        if(isset($address['building']) && trim($address['building']) != '') {
            $addressStr .= $address['building'].', ';
            $addressHidden['building'] = $address['building'];
        } else {
            if(!$showEmpty) {
                $addressStr .= '';
            }
            $addressHidden['building'] = '';
        }

        if(isset($address['flat']) && trim($address['flat']) != '') {
            $addressStr .= $address['flat'].', ';
            $addressHidden['flat'] = $address['flat'];
        } else {
            if(!$showEmpty) {
                $addressStr .= '';
            }
            $addressHidden['flat'] = '';
        }

        if(isset($address['postindex']) && trim($address['postindex']) != '') {
            $addressStr .= 'почтовый индекс '.$address['postindex'];
            $addressHidden['flat'] = $address['postindex'];
        } else {
            if(!$showEmpty) {
                $addressStr .= '';
            }
            $addressHidden['postindex'] = '';
        }

        return array(
            'addressStr' => $addressStr,
            'addressHidden' => CJSON::encode($addressHidden)
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
        $formModel->policy = ($oms->oms_series != null  && $oms->type == 5) ? $oms->oms_series.$oms->oms_number : $oms->oms_number;
        $formModel->omsSeries = $oms->oms_series;
        $formModel->gender = $oms->gender;
        $formModel->birthday = $oms->birthday;
        $formModel->id = $oms->id;
        $formModel->omsType = $oms->type;
        $formModel->policyGivedate = $oms->givedate;
        $formModel->policyEnddate = $oms->enddate;
        $formModel->status = $oms->status;

        // Если омс - 0, то подтягиваем его в тип 1
        if ($formModel->omsType == 0)
            $formModel->omsType = 1;

        // Если тип = 0, то
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
        //var_dump($data);
        //exit();
        echo CJSON::encode(array('success' => true,
                                 'data' => $data));
    }

    private function prepareOms() {
        $modelOms = new Oms();
        $oms = $modelOms->findByPk($_GET['omsid']);
        if($oms == null) {
            $req = new CHttpRequest();
            $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/reception/patient/viewsearch'));
        }
		
		// Подгружаем на всякий случай данные из ТАСУ
		$tasuStatus = true;
		try {
			$tasuController = Yii::app()->createController('admin/tasu');
			$tasuOmsData = $tasuController[0]->getTasuPatientByPolicy($oms);
			/*if($tasuOmsData === -1) {
				$tasuStatus = false;
			}*/
		} catch(Exception $e) {
			$tasuStatus = false;
		}
        if($oms['status'] != 6) {
			// Если статус = 0, то поправить на статус = 0
			if ($oms['status']==0)
			{
				$oms['status']=1;
			}
		} else { // Иначе подгружаем из ТАСУ
		
		}
		
        // Прочитаем название страховой компании
        $omsInsurance =  new Insurance();
        $insuranceObject = $omsInsurance ->findByPk($oms['insurance']);
        $insuranceName = $insuranceObject['name'];

        // Прочитаем регион

        $omsRegion =  new CladrRegion();
        $regionObject = $omsRegion ->findByPk($oms['region']);
        $regionName = $regionObject['name'];


        $formModel = new FormOmsEdit();
        $formModel = $this->fillOmsModel($formModel, $oms);
        return array(
          'formModel' => $formModel,
          'oms' => $oms,
          'insuranceId' => $oms['insurance'],
          'insuranceName' => $insuranceName,
          'regionId' => $oms['region'],
          'regionName' => $regionName,
		  'tasuStatus' => $tasuStatus
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
            $model->insurance = $_POST['FormOmsEdit']['insurance'];
            $model->region = $_POST['FormOmsEdit']['region'];
            //var_dump($_POST['FormOmsEdit']['omsType']);
            //exit();

            //var_dump($model);
            //exit();
            // Если не задан номер полиса, то нужно занести в поле "тип" модели недействительный ИД
            if (!isset($_POST['FormOmsEdit']['omsType']))
            {
                $model->omsType = -1;
            }

            if($model->validate()) {
                // Проверяем на существование такого же полиса
                $oms = $this->checkUniqueOms($model, true);
                if($oms == null) {
                    $oms = Oms::model()->findByPk($_POST['FormOmsEdit']['id']);
                    $foundOmsMsg = null;
                    $this->addEditModelOms($oms, $model);
                } else {
                    // В этом случае полис существует. Надо обновить на новые данные и удалить старый полис (для того, чтобы не было дубликатов
                   // Oms::model()->deleteByPk($_POST['FormOmsEdit']['id']);
                    $birthday = implode('.', array_reverse(explode('-', $model->birthday)));
                    $foundOmsMsg = 'Найден другой полис с таким номером (<strong class="bold">'.$oms->last_name.' '.$oms->first_name.' '.$oms->middle_name.', дата рождения '.$birthday.'</strong>)';
                    // Ищем медкарты с таким ОМС и просто переставляем ID, только_если у того, кого удаляют, нет медкарт. В противном случае, ничего не делаем
                    // Если у старого пациента нет карт (редактировали ОМС без карты), а у нового (совпавшего) есть - подцепляем карты
                   /* $medcardsByDelete = Medcard::model()->findAll('policy_id = :policy_id', array(':policy_id' => $_POST['FormOmsEdit']['id']));
                    if(count($medcardsByDelete) == 0) {
                        $medcardsForReplace = Medcard::model()->findAll('policy_id = :policy_id', array(':policy_id' => $oms->id));
                        foreach($medcardsForReplace as $medcardForReplace) {
                            $medcardModel = Medcard::model()->findByPk($medcardForReplace['card_number']);
                            if($medcardModel != null) {
                                $medcardModel->policy_id = $oms->id;
                                if(!$medcardModel->save()) {
                                    echo CJSON::encode(array(
                                        'success' => 'false',
                                        'errors' => array(
                                            'medcard' => array(
                                                'Не могу прикрепить медкарту к найденному пациенту!'
                                            )
                                        )
                                    ));
                                    exit();
                                }
                            }
                        }
                    } */
                }
                echo CJSON::encode(array('success' => 'true',
                                         'foundOmsMsg' => $foundOmsMsg,
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
            // Записываем текущую дату и ID пользователя, который создал медкарту
            $medcard->date_created =  date('Y-m-d H:i:s');
            $record = User::model()->findByAttributes(array('id' => Yii::app()->user->id));
            $medcard->user_created = $record['employee_id'];

        }
        $medcard->snils = $model->snils;
        $medcard->address = $model->addressHidden;
		$medcard->address_str = $model->address;
		$medcard->address_reg = $model->addressRegHidden;
		$medcard->address_reg_str = $model->addressReg;
        $medcard->doctype = $model->doctype;
        $medcard->serie = $model->serie;
        $medcard->docnumber = $model->docnumber;
       // $medcard->who_gived = $model->whoGived;
        //$medcard->gived_date = $model->documentGivedate;
        $medcard->invalid_group = $model->invalidGroup;
        $medcard->reg_date = date('Y-m-d');
        $medcard->work_place = $model->workPlace;
        $medcard->work_address = $model->workAddress;
        $medcard->post = $model->post;
        $medcard->contact = $model->contact;
        $medcard->profession = $model->profession;
        $medcard->enterprise_id = 1; // TODO: сделать выборку из учреждений, сейчас ставим мониаг жёстко

        if($oms) {
			if(is_array($oms)) {
				$medcard->policy_id = $oms['id'];
			} else {
				$medcard->policy_id = $oms->id;
			}
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

    // Ищет всех записанных
    public function actionSearchAllWritten() {
        $filters = $this->checkFilters();

        $rows = $_GET['rows'];
        $page = $_GET['page'];
        $sidx = $_GET['sidx'];
        $sord = $_GET['sord'];


        $somePatientToSelect = new Patient();
        $num = $somePatientToSelect->getNumRowsWritten($filters, false, false, false, false);

        if(count($num) > 0) {
            $totalPages = ceil($num / $rows);
            $start = $page * $rows - $rows;
            $items = $somePatientToSelect->getRowsWritten($filters, $sidx, $sord, $start, $rows);
        } else {
            $items = array();
            $totalPages = 0;
        }

        //var_dump($items);
        //exit();
        echo CJSON::encode(
            array(
                'success' => true,
                'rows' => $items,
                'total' => $totalPages,
                'records' => count($num)
            )
        );
    }

    public function actionSearchMediate() {
        $filters = $this->checkFilters();

        $rows = $_GET['rows'];
        $page = $_GET['page'];
        $sidx = $_GET['sidx'];
        $sord = $_GET['sord'];

        $model = new MediatePatient();
        $num = $model->getRows($filters, false, false, false, false);

        if(count($num) > 0) {
            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;
            $items = $model->getRows($filters, $sidx, $sord, $start, $rows);
        } else {
            $items = array();
            $totalPages = 0;
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

    // Поиск пациента и его запсь
    public function actionSearch() {
       // var_dump($_GET);
       // exit();

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

        if((isset($_GET['mediateonly'])) && ($_GET['mediateonly'] == 0)) {
            $mediateOnly = true;
        } else {
            $mediateOnly = false;
        }

        if(isset($_GET['withmediate']) && $_GET['withmediate'] == 0) {
            $withMediate = true;
        } else {
            $withMediate = false;
        }


        if(isset($_GET['onlyingreetings']) && $_GET['onlyingreetings'] == 1) {
            $onlyInGreetings = true;
        } else {
            $onlyInGreetings = false;
        }

        if(isset($_GET['cancelled']) && $_GET['cancelled'] == 1) {
            $cancelledGreetings = true;

        } else {
            $cancelledGreetings = false;
        }
		
		$onlyClosedGreetings = false;
		$greetingDate = false;
		foreach($filters['rules'] as $key => $rule) {
			if($rule['field'] == 'status') {
				unset($filters['rules'][$key]);
				$onlyClosedGreetings = true;
			}
			if($rule['field'] == 'patient_day') {
				$greetingDate = true;
			}
		}

        if(!$mediateOnly) {
            $model = new Oms();
            // Вычислим общее количество записей

            $num = $model->getNumRows($filters,false,false,false,false,$WithOnly,$WithoutOnly, $onlyInGreetings,$cancelledGreetings, $onlyClosedGreetings, $greetingDate);

            $totalPages = ceil($num['num'] / $rows);
            $start = $page * $rows - $rows;
            $items = $model->getRows($filters, $sidx, $sord, $start, $rows, $WithOnly, $WithoutOnly, $onlyInGreetings, $cancelledGreetings, $onlyClosedGreetings, $greetingDate);
			$now = time();
            // Обрабатываем результат
            foreach($items as $index => &$item) {
                if($item['reg_date'] != null) {
                    $parts = explode('-', $item['reg_date']);
                    $item['reg_date'] = $parts[0];
                } else {
					// Можно вычленить из карты
					if($item['card_number'] != null) {
						$cardYear = mb_substr($item['card_number'], strrpos($item['card_number'], '/') + 1);
						$item['reg_date'] = '20'.$cardYear;
					}
				}
				
				$currentDate = new DateTime(date("Y-m-d"));
                if($item['birthday'] != null) {
                    $parts = explode('-', $item['birthday']);
                    $item['birthday'] = $parts[2].'.'.$parts[1].'.'.$parts[0];
					// Считаем возраст
					$datetime = new DateTime($item['birthday']);
					$interval = $datetime->diff($currentDate);
					$fullYears = $interval->format("%Y");
					$datetime = new DateTime($parts[2].'.'.$parts[1].'.'.($parts[0] + $fullYears));
					$interval = $datetime->diff($currentDate);
					$fullMonths = $interval->format("%m");
					$datetime = new DateTime($parts[2].'.'.$fullMonths.'.'.($parts[0] + $fullYears));
					$interval = $datetime->diff($currentDate);
					$fullDays = $interval->format("%d");
					$item['grow'] = $fullYears.' лет, '.$fullMonths.' месяцев, '.$fullDays.' дней';
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
                $num = array();
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

        $canViewGreetingHistory =  Yii::app()->user->checkAccess('canViewGreetingArchive');
		if(is_array($num) && isset($num['num'])) {
			$num = $num['num'];
		} elseif(is_array($num)) {
			$num = count($num);
		}
	
       echo CJSON::encode(array(
			'greetingsHistory' => $canViewGreetingHistory,
			'success' => true,
			'rows' => $items,
			'total' => 1,
			'records' => $num
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
            if(isset($filter['data'])) {
				if(!is_array($filter['data']) && trim($filter['data']) != '') {
					$allEmpty = false;
				}
				if(is_array($filter['data']) && count($filter['data']) > 0) {
					$allEmpty = false;
				}
            }

            if($filter['field'] == 'oms_number' && trim($filter['data']) != '') {
                //---->
                // Нужно добавить поле к фильтру для поиска по конкатенированному полю серии и номера
                $filters['rules'][] = array(
                    'field' => 'normalized_oms_number',
                    'op' => 'eq',
                    'data' => str_replace(array('-',' '), '', $filter['data'])
                );
                //---->
                if(mb_strlen($filter['data']) != 16) {
                    unset($filter);
                    continue;
                }
                // Создаём два дополнительных фильтра по ОМС
                $filters['rules'][] = array(
                    'field' => 'e_oms_number',
                    'op' => 'eq',
                    'data' => ' '.$filter['data']
                );

                $filters['rules'][] = array(
                    'field' => 'k_oms_number',
                    'op' => 'eq',
                    'data' => mb_substr($filter['data'], 0, 6).' '.mb_substr($filter['data'], 6)
                );
            }
        }

        if($allEmpty) {
            echo CJSON::encode(array(
					'success' => false,
                    'data' => 'Задан пустой поисковой запрос.'
				)
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
    public function actionWritePatientStepOne($callcenter = 0) {
        if(isset($_GET['waitingline']) && $_GET['waitingline'] == 1) {
            $waitingLine = 1;
        } else {
            $waitingLine = 0;
        }
        $this->render('writePatient1', array(
            'privilegesList' => $this->getPrivileges(),
            'modelMedcard' => new FormPatientWithCardAdd(),
            'modelOms' => new FormOmsEdit(),
            'callcenter' => $callcenter,
            'waitingLine' => $waitingLine,
            'maxInWaitingLine' => Setting::model()->find('name = :name', array(':name' => 'maxInWaitingLine'))->value
        ));
    }

    // Экшн записи пациента: шаг 2
    public function actionWritePatientStepTwo() {
        if(isset($_GET['cardid'])) {
            // Проверим, что такая карта реально есть
            $medcard = Medcard::model()->findByPk($_GET['cardid']);
            $tasuStatus = true;
			if($medcard != null) {
                $oms = Oms::model()->findByPk($medcard['policy_id']);
				if($oms != null) {
					// Подгрузка из ТАСУ, если нужно
					if($oms->region == null || $oms->insurance == null) {
						try {
							$tasuController = Yii::app()->createController('admin/tasu');
							$result = $tasuController[0]->getTasuPatientByPolicy($oms);
							/*if($result === -1) {
								$tasuStatus = false;
							}*/
						} catch(Exception $e) {
							$tasuStatus = false;
						}
					}
				} else {
					throw new Exception('Не найден полис!');
				}
            } else {
                throw new Exception('Не найдена медкарта!');
            }
			if(isset($_GET['callcenter']) && $_GET['callcenter'] == 1) {
				$callcenter = 1;
			} else {
				$callcenter = 0;
			}

            if(isset($_GET['is_pregnant']) && (int)$_GET['is_pregnant'] == 1) {
                $isPregnant = 1;
            } else {
                $isPregnant = 0;
            }

            if(isset($_GET['waitingline']) && $_GET['waitingline'] == 1) {
                $waitingLine = 1;
            } else {
                $waitingLine = 0;
            }

            if($medcard != null) {
                $answer = array();
                if(isset($_GET['greeting_id'])) {
                    $currentGreeting = SheduleByDay::model()->findByPk($_GET['greeting_id']);
                    // Крайне слабая проверка. Надо думать.
                    if($currentGreeting->medcard_id == $_GET['cardid']) {
                        $answer['greetingId'] = $currentGreeting->id;
                        $answer['greetingDate'] =  $currentGreeting->patient_day;
                        $answer['greetingType'] = $currentGreeting->greeting_type;
                        $doctorModel = Doctor::model()->findByPk($currentGreeting->doctor_id);
                        if($doctorModel != null) {
                            $answer['doctorFio'] = $doctorModel->last_name.' '.$doctorModel->first_name.' '.$doctorModel->middle_name;
                            if($doctorModel->post_id != null) {
                                $medworker = Medworker::model()->findByPk($doctorModel->post_id);
                                if($medworker != null) {
                                    $isPregnant = $medworker->is_for_pregnants;
                                }
                            }
                        } else {
                            $answer['doctorFio'] = '';
                        }

                        if($currentGreeting->mediate_id != null) { // Записан опосредованный
                            $mediateModel = MediatePatient::model()->findByPk($currentGreeting->mediate_id);
                            if($mediateModel != null) {
                                $answer['patientFirstName'] = $mediateModel->first_name;
                                $answer['patientLastName'] = $mediateModel->last_name;
                                $answer['patientMiddleName'] = $mediateModel->middle_name != null ? $mediateModel->middle_name : '';
                                $answer['patientComment'] = $currentGreeting->comment;
                                $answer['patientPhone'] = $mediateModel->phone != null ? $mediateModel->phone : '+7';
                            } else {
                                $answer['patientFirstName'] = ''; // Хотя здесь могут быть данные
                                $answer['patientLastName'] = '';
                                $answer['patientMiddleName'] = '';
                                $answer['patientComment'] = $currentGreeting->comment;
                                $answer['patientPhone'] = '+7';
                            }
                        } else {
                            $answer['patientFirstName'] = '';
                            $answer['patientLastName'] = '';
                            $answer['patientMiddleName'] = '';
                            $answer['patientComment'] = '';
                            $answer['patientPhone'] = '+7';
                        }
                    }
                } else {
                    $answer['patientFirstName'] = '';
                    $answer['patientLastName'] = '';
                    $answer['patientMiddleName'] = '';
                    $answer['patientComment'] = '';
                    $answer['patientPhone'] = '+7';
                }

                $answer += array(
                    'wardsList' => $this->getWardsList(),
                    'postsList' => $this->getPostsList(),
                    'medcard' => $medcard,
                    'oms' => $oms,
                    'waitingLine' => $waitingLine,
                    'maxInWaitingLine' => Setting::model()->find('name = :name', array(':name' => 'maxInWaitingLine'))->value,
                    'isPregnant' => $isPregnant,
                    'callcenter' => $callcenter,
                    'calendarType' => Setting::model()->find('name = :name', array(':name' => 'calendarType'))->value
                );


                // Если есть параметр unwritedGreetingId, то надо вытащить параметры для подстановки в форму записи
                $unwritedGreeting  = null;
                if (isset($_GET['unwritedGreetingId']))
                {
                    $unwritedGreeting = $this->readUnwritedGreeting($_GET['unwritedGreetingId']);
                }
                if ($unwritedGreeting  !=null)
                {
                    $answer['commentToWrite'] = $unwritedGreeting['comment'];
                }
                else
                {
                    $answer['commentToWrite'] = '';
                }
                $answer['cancelledGreeting'] = '';

                if (isset ($_GET['cancelledGreetingId']))
                {
                    // Нужно подать данные о приёме, который отменён
                    $answer['cancelledGreeting'] = $_GET['cancelledGreetingId'];
                    // Читаем данные об отменённом приёме по Id
                    $cancelledGreeting = CancelledGreeting::model()->findByPk($_GET['cancelledGreetingId']);

                    if ($cancelledGreeting['mediate_id']!='' && $cancelledGreeting['mediate_id']!=null)
                    {
                        // Читаем посредованного пациента
                        $mPatient = MediatePatient::model()->findByPk($cancelledGreeting['mediate_id']);
                        // Записываем ФИО опосредованного пациента и его телефон
                        $answer['patientFirstName'] = $mPatient['first_name'];
                        $answer['patientLastName'] = $mPatient['last_name'];
                        $answer['patientMiddleName'] = $mPatient['middle_name'];
                        $answer['patientPhone'] = $mPatient['phone'];
                    }
                    // Иначе нужен только комментарий, а мы его протаскиваем в обоих случаях
                    $answer['patientComment'] = $cancelledGreeting['comment'];

                }


				if(isset($tasuStatus)) {
					$answer['tasuStatus'] = $tasuStatus;
				} else {
					$answer['tasuStatus'] = false;
				}
                $this->render('writePatient2', $answer);
                exit();
            }
        }

        $req = new CHttpRequest();
        $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/reception/patient/writepatientstepone'));
    }

    public function actionChangeOrDelete()
    {
        if(isset($_GET['callcenter']) && $_GET['callcenter'] == 1) {
            $callcenter = 1;
        } else {
            $callcenter = 0;
        }
        $this->render('changeRecord', array(
            'callcenter' => $callcenter
        ));
    }

    private function getWardsList() {
        // Список отделений
        $ward = new Ward();
        $wardsResult = $ward->getRows(false, 'name', 'asc');
        $wardsList = array('-1' => 'Нет');
        foreach($wardsResult as $key => $value) {
            $wardsList[$value['id']] = $value['name'];
        }
        return $wardsList;
    }

    private function getPostsList() {
        // Список должностей
        $post = new Post();
        $postsResult = $post->getRows(false, 'name', 'asc');
        $postsList = array('-1' => 'Нет');
        foreach($postsResult as $key => $value) {
            $postsList[$value['id']] = $value['name'];
        }
        return $postsList;
    }

    private function readUnwritedGreeting($greetingToFind)
    {
        $result = null;
        // Перебираем массив сессии и проверяем на равенство $greetingToFind и id приёма
        foreach($_SESSION['unwritedGreetings'] as $oneGreeting)
        {
            if ($oneGreeting['id']==$greetingToFind)
            {
                $result = $oneGreeting;
            }

        }
        return $result;
    }


    public function actionDeleteCancelledGreeting()
    {
        // Вытаскиваем из запроса ИД приёма
        if (isset($_GET['greetingId']))
        {
            CancelledGreeting::deleteCancelledGreeting($_GET['greetingId']);
        }

    }

    // Запись опосредованного пациента
    public function actionWritePatientWithoutData($callcenter = false) {
        //var_dump($_SESSION);
        //exit();

        $answer = array(
            'wardsList' => $this->getWardsList(),
            'postsList' => $this->getPostsList(),
            'callcenter' => (int)$callcenter,
            'calendarType' => Setting::model()->find('name = :name', array(':name' => 'calendarType'))->value
        );
        if(isset($_GET['greeting_id'])) {
            $currentGreeting = SheduleByDay::model()->findByPk($_GET['greeting_id']);
            $answer['greetingId'] = $currentGreeting->id;
            $answer['greetingDate'] =  $currentGreeting->patient_day;
            $doctorModel = Doctor::model()->findByPk($currentGreeting->doctor_id);
            if($doctorModel != null) {
                $answer['doctorFio'] = $doctorModel->last_name.' '.$doctorModel->first_name.' '.$doctorModel->middle_name;
            } else {
                $answer['doctorFio'] = '';
            }

            if($currentGreeting->mediate_id != null) { // Записан опосредованный
                $mediateModel = MediatePatient::model()->findByPk($currentGreeting->mediate_id);
                if($mediateModel != null) {
                    $answer['patientFirstName'] = $mediateModel->first_name;
                    $answer['patientLastName'] = $mediateModel->last_name;
                    $answer['patientMiddleName'] = $mediateModel->middle_name != null ? $mediateModel->middle_name : '';
                    $answer['patientComment'] = $currentGreeting->comment;
                    $answer['patientPhone'] = $mediateModel->phone != null ? $mediateModel->phone : '+7';
                } else {
                    $answer['patientFirstName'] = ''; // Хотя здесь могут быть данные
                    $answer['patientLastName'] = '';
                    $answer['patientMiddleName'] = '';
                    $answer['patientComment'] = $currentGreeting->comment;
                    $answer['patientPhone'] = '+7';
                }
            } else {
                $answer['patientFirstName'] = '';
                $answer['patientLastName'] = '';
                $answer['patientMiddleName'] = '';
                $answer['patientComment'] = '';
                $answer['patientPhone'] = '+7';
            }
        } else {
            $answer['patientFirstName'] = '';
            $answer['patientLastName'] = '';
            $answer['patientMiddleName'] = '';
            $answer['patientComment'] = '';
            $answer['patientPhone'] = '+7';
        }
        // Если есть параметр unwritedGreetingId, то надо вытащить параметры для подстановки в форму записи
        $unwritedGreeting  = null;
        if (isset($_GET['unwritedGreetingId']))
        {
            $unwritedGreeting = $this->readUnwritedGreeting($_GET['unwritedGreetingId']);
        }
        if ($unwritedGreeting  !=null)
        {
            $answer['patientFirstName'] = $unwritedGreeting['first_name'];
            $answer['patientMiddleName'] = $unwritedGreeting['middle_name'];
            $answer['patientLastName'] = $unwritedGreeting['last_name'];
            $answer['patientPhone'] = $unwritedGreeting['phone'];
            $answer['patientComment'] = $unwritedGreeting['comment'];
           // var_dump($answer);
           // exit();
        }
        else
        {

            $answer['patientFirstName'] = '';
            $answer['patientMiddleName'] = '';
            $answer['patientLastName'] = '';
            $answer['patientPhone'] = '+7';
            $answer['patientComment'] = '';
        }

        $answer['cancelledGreeting'] = '';

        if (isset ($_GET['cancelledGreetingId']))
        {
            // Нужно подать данные о приёме, который отменён
            $answer['cancelledGreeting'] = $_GET['cancelledGreetingId'];
            // Читаем данные об отменённом приёме по Id
            $cancelledGreeting = CancelledGreeting::model()->findByPk($_GET['cancelledGreetingId']);

            if ($cancelledGreeting['mediate_id']!='' && $cancelledGreeting['mediate_id']!=null)
            {
                // Читаем посредованного пациента
                $mPatient = MediatePatient::model()->findByPk($cancelledGreeting['mediate_id']);
                // Записываем ФИО опосредованного пациента и его телефон
                $answer['patientFirstName'] = $mPatient['first_name'];
                $answer['patientLastName'] = $mPatient['last_name'];
                $answer['patientMiddleName'] = $mPatient['middle_name'];
                $answer['patientPhone'] = $mPatient['phone'];
            }
            // Иначе нужен только комментарий, а мы его протаскиваем в обоих случаях
            $answer['patientComment'] = $cancelledGreeting['comment'];

        }


        $this->render('writepatientwithoutdata', $answer);
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
                                     'data' => 'Нехватка данных: нет медкарты в базе!'));
            exit();
        }
		
		$oms = Oms::model()->findByPk($medcard->policy_id);
		if($oms == null) {
			echo CJSON::encode(array('success' => 'false',
                                     'data' => 'Нехватка данных: нет полиса в базе!'));
            exit();
		}
		// Если нет данных по полису (региона и страховой компании), то их надо подгрузить
		$tasuStatus = true;
		if($oms->region == null || $oms->insurance == null) {
			try {
				$tasuController = Yii::app()->createController('admin/tasu');
				$result = $tasuController[0]->getTasuPatientByPolicy($oms);
				/*if($result === -1) {
					$tasuStatus = false;	
				}*/
			} catch(Exception $e) {
				$tasuStatus = false;
			}
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
								 'tasuStatus' => $tasuStatus,
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