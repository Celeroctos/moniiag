<?php
class PrintController extends Controller {
    public $layout = 'print';
    public $responseData = array();
/*
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
            $groups = array('','I', 'II', 'III', 'IV');
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
        // Записываем insurance_name в oms
        if ($oms['insurance']!='' && $oms['insurance']!=null)
        {
            $insurance = Insurance::model()->findByPk($oms->insurance);
            $oms['insurance'] = $insurance->name;
        }

        foreach($privileges as &$priv) {
            $priv['docgivedate'] = $this->formatDate($priv['docgivedate']);
            $privModel = Privilege::model()->findByPk($priv->privilege_id);
            $priv['docname'] = '(Код '.$privModel->code.') '.$priv['docname'];
        }

        // Превращаем адрес медкарты
        $patientController = Yii::app()->createController('reception/patient');
		$addressData = $patientController[0]->getAddressStr($medcard['address'],true);
        $medcard['address'] = $addressData['addressStr'];

		$addressRegData = $patientController[0]->getAddressStr($medcard['address_reg'],true);
        $medcard['address_reg'] = $addressData['addressStr'];


        $mPDF = Yii::app()->ePdf->mpdf('', 'A5-L', 0, '', 8, 8, 8, 8, 0, 0);
        $stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css').'/print.css');
        $mPDF->WriteHTML($stylesheet, 1);
        $mPDF->WriteHTML(

            $this->render('index', array('medcard' => $medcard,
                'oms' => $oms,
                'enterprise' => $enterprise,
                'privileges' => $privileges),true)

        );

        $this->render('greetingpdf', array(
            'pdfContent' => $mPDF->Output()
        ));
    }
*/
    public function actionPrintMainPage() {
        // Выбираем всю информацию о медкарте
        if(isset($_GET['medcardid'])) {

        }
        $medcard = Medcard::model()->findByPk($_GET['medcardid']);
        if($medcard == null) {
            exit('Ошибка! Не выбрана медкарта.');
        }
        if($medcard['invalid_group'] != 0 && $medcard['invalid_group'] != null) {
            $groups = array('','I', 'II', 'III', 'IV');
            $medcard['invalid_group'] = $groups[$medcard['invalid_group']].' группа';
        } else {
            $medcard['invalid_group'] = 'Нет группы';
        }
        // Выбираем ОМС по медкарте
        $oms = Oms::model()->findByPk($medcard->policy_id);
        if($oms == null) {
            exit('Ошибка! Полиса не существует!');
        }
		// Подгрузка из ТАСУ
		if($oms->region == null || $oms->insurance == null) {
			$tasuController = Yii::app()->createController('admin/tasu');
			$tasuController[0]->getTasuPatientByPolicy($oms);
		}

        // Если у полиса есть поле "серия" - конкатэнируем его с номером через пробел и выводим.
        //         Иначе выводим только номер без конкатенации
        if ($oms->oms_series != '' && $oms->oms_series!= null)
        {
            $oms->oms_number = $oms->oms_series. " " .$oms->oms_number;
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
        // Записываем insurance_name в oms
        if ($oms['insurance']!='' && $oms['insurance']!=null)
        {
            $insurance = Insurance::model()->findByPk($oms->insurance);
            $regions = InsuranceRegion::findRegions($oms['insurance']);
           // var_dump($regions );
           // exit();
            if ($regions != null && count($regions)>0)
            {
                $regionId = $regions[0]['id'];
                $omsRegion =  new CladrRegion();
                $regionObject = $omsRegion ->findByPk($regionId );
                $oms['region'] = $regionObject['name'];
            }
            else
            {
                $oms['region'] = '';
            }
            $oms['insurance'] = $insurance->name;
        }
        else
        {
            $oms['region'] = '';
        }

        // Смотрим - какой тип и статус у полиса
        $statusId = $oms['status'];
        if ($statusId == 0)
            $statusId = 1;

        $status = OmsStatus::model()->findByPk($statusId);
        $oms['status'] = $status['name'];

        $typeId = $oms['type'];
        if ($typeId == 0)
            $typeId = 1;

        $type = OmsType::model()->findByPk($typeId);
        $oms['type'] = $type ['name'];
        //var_dump($oms);
        //exit();
        // Прочитаем регион
        /*if ($oms['region']!='' && $oms['region']!=null)
        {
            $omsRegion =  new CladrRegion();
            $regionObject = $omsRegion ->findByPk($oms['region']);
            $oms['region'] = $regionObject['name'];
        }*/

        foreach($privileges as &$priv) {
            $priv['docgivedate'] = $this->formatDate($priv['docgivedate']);
            $privModel = Privilege::model()->findByPk($priv->privilege_id);
            $priv['docname'] = '(Код '.$privModel->code.') '.$priv['docname'];
        }

        // Превращаем адрес медкарты
        $patientController = Yii::app()->createController('reception/patient');
        $addressData = $patientController[0]->getAddressStr($medcard['address'],true);
        $medcard['address'] = $addressData['addressStr'];

        $addressRegData = $patientController[0]->getAddressStr($medcard['address_reg'],true);
        $medcard['address_reg'] = $addressData['addressStr'];


        $mPDF = Yii::app()->ePdf->mpdf('', 'A5-L', 0, '', 0, 0, 0, 0, 0, 0);
        $stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css').'/print.css');


        $this->render('index', array('medcard' => $medcard,
            'oms' => $oms,
            'enterprise' => $enterprise,
            'privileges' => $privileges)

        );


    }

    public function formatDate($date) {
        if($date == null) {
            return '';
        }
        $parts = explode('-', $date);
        return $parts[2].'.'.$parts[1].'.'.$parts[0];
    }

    public function actionGetRecommendationTemplatesInGreeting($greetingId)
    {
        echo CJSON::encode(array('success' => 'true',
            'data' => MedcardElementForPatient::getRecommendationTemplatesInGreeting($greetingId)
        ));
        exit();
    }

    // Старый код, убрать потом
    /*
    // Печать результа приёма
	public function actionPrintGreeting($greetingIn = false, $printRecom=false, $returnResult = false) {
        if($greetingIn === false && !isset($_GET['greetingid'])) {
            exit('Ошибка: не выбран приём.');
        } else {
            $greetingId = $greetingIn !== false ? $greetingIn : $_GET['greetingid'];
        }
        // В противном случае, выбираем все элементы, изменённые во время приёма
		
		$greeting = SheduleByDay::model()->findByPk($greetingId);
		if($greeting == null) {
			exit('Ошибка: такого приёма не существует!');
		}
		
		// Получим общую информацию о приёме
		$doctor = Doctor::model()->findByPk($greeting['doctor_id']);
		$greetingInfo['doctor_fio'] = $doctor['last_name'].' '.$doctor['first_name'].' '.$doctor['middle_name'];
		// Найдём медкарту, а по ней и пациента
		$medcard = Medcard::model()->findByPk($greeting['medcard_id']);
		$patient = Oms::model()->findByPk($medcard['policy_id']);
		$greetingInfo['patient_fio'] = $patient['last_name'].' '.$patient['first_name'].' '.$patient['middle_name'];
		$greetingInfo['card_number'] = $greeting['medcard_id'];
		$dateParts = explode('-', $greeting['patient_day']);
		$greetingInfo['date'] = $dateParts[2].'.'.$dateParts[1].'.'.$dateParts[0];
		
		//var_dump($printRecom);
		//exit();
		
		if (!$printRecom)
		{
			$changedElements = MedcardElementForPatient::model()->findAllPerGreeting($greetingId);
		}
		else
		{
			$changedElements = MedcardElementForPatient::model()->findAllPerGreeting($greetingId,false,'eq',true);
		}

        //var_dump($changedElements );
        //exit();

      //  foreach ($changedElements as $oneEl)
     //   {
    //        var_dump($oneEl['value'] .' '.$oneEl['element_id']);
  //      }
//exit();
        if(count($changedElements) == 0) {
            // Единичная печать
            if($greetingIn === false) {
				//var_dump($changedElements);
				//exit();
				
                exit('Во время этого приёма не было произведено никаких изменений!');
            } else {
                return array();
            }
        }
		
		// Создадим виджет 
		$categorieWidget = $this->createWidget('application.modules.doctors.components.widgets.CategorieViewWidget');
		
		//var_dump($changedElements);
		//exit();

		// Запихнём виджету те элементы, которые мы вытащили по приёму
		$categorieWidget->setHistoryElements($changedElements);
		
		// Провернём как в мясорубке элементы в этом виджете
		$categorieWidget->makeTree('getTreeNodePrint');
		$categorieWidget->sortTree();
		// Теперь поделим категории
		$categorieWidget->divideTreebyCats();

		$sortedElements = $categorieWidget->dividedCats;

        // Вытащим диагнозы
        //var_dump($greetingId);
        //exit();

        $pd = PatientDiagnosis::model()->findDiagnosis($greetingId, 0);
        $sd = PatientDiagnosis::model()->findDiagnosis($greetingId, 1);
        $cd = PatientDiagnosis::model()->findDiagnosis($greetingId, 2);
        $cpd = ClinicalPatientDiagnosis::model()->findDiagnosis($greetingId, 0);
        $csd = ClinicalPatientDiagnosis::model()->findDiagnosis($greetingId, 1);
        $noteDiagnosis = $greeting['note'];

        //var_dump($cd);
        //exit();

        // Соберём их в об'ект
        $diagnosises = array(
            'primary' => $pd,
            'secondary' => $sd,
            'clinicalPrimary' => $cpd,
            'clinicalSecondary' => $csd,
            'complicating' => $cd,
            'noteGreeting' => $noteDiagnosis
        );

        //var_dump($diagnosises );
        //exit();

        //var_dump($sortedElements);
        //exit();
		if($greetingIn === false) {
            if(!$returnResult) {
                $mPDF = Yii::app()->ePdf->mpdf('', 'A5-L');

                $stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css').'/print.css');
                $mPDF->WriteHTML($stylesheet, 1);

                $stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css').'/print.less');
                $mPDF->WriteHTML($stylesheet, 1);


                $stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css').'/paper.less');
                $mPDF->WriteHTML($stylesheet, 1);
                $htmlForPdf =
                    $this->render('greeting', array(
                        'templates' => $sortedElements,
                        'greeting' => $greetingInfo,
                        'diagnosises' => $diagnosises
                    ), true);
                $mPDF->WriteHTML(
                    $htmlForPdf
                );

                ob_end_clean();
                $this->render('greetingpdf', array(
                    'pdfContent' => $mPDF->Output()
                ));
            } else {
                return array(
                    'templates' => $sortedElements,
                    'greeting' => $greetingInfo,
                    'diagnosises' => $diagnosises
                );
            }
		} else {
			return array(
                'templates' => $sortedElements,
                'greeting' => $greetingInfo,
                'diagnosises' => $diagnosises
            );
		}
    }*/


    // Печать результа приёма
    public function actionPrintGreeting($greetingIn = false, $printRecom=false, $returnResult = false, $templateId=false) {
        if($greetingIn === false && !isset($_GET['greetingid'])) {
            exit('Ошибка: не выбран приём.');
        } else {
            $greetingId = $greetingIn !== false ? $greetingIn : $_GET['greetingid'];
        }
        // В противном случае, выбираем все элементы, изменённые во время приёма

        $greeting = SheduleByDay::model()->findByPk($greetingId);
        if($greeting == null) {
            exit('Ошибка: такого приёма не существует!');
        }

        // Получим общую информацию о приёме
        $doctor = Doctor::model()->findByPk($greeting['doctor_id']);
        $greetingInfo['doctor_fio'] = $doctor['last_name'].' '.$doctor['first_name'].' '.$doctor['middle_name'];
        // Найдём медкарту, а по ней и пациента
        $medcard = Medcard::model()->findByPk($greeting['medcard_id']);
        $patient = Oms::model()->findByPk($medcard['policy_id']);
        $parts = explode('-', $patient['birthday']);

        $greetingInfo['full_years'] = date('Y') - $parts[0];
        //var_dump($greetingInfo['full_years'] );
       // exit();
        $enterprise = Enterprise::model()->findByPk($medcard->enterprise_id);
        $greetingInfo['patient_fio'] = $patient['last_name'].' '.$patient['first_name'].' '.$patient['middle_name'];
        $greetingInfo['card_number'] = $greeting['medcard_id'];
        $dateParts = explode('-', $greeting['patient_day']);
        $greetingInfo['date'] = $dateParts[2].'.'.$dateParts[1].'.'.$dateParts[0];

        //var_dump($printRecom);
        //exit();

        if (!$printRecom)
        {
            $changedElements = MedcardElementForPatient::model()->findAllPerGreeting($greetingId);
        }
        else
        {
            // $changedElements = MedcardElementForPatient::model()->findAllPerGreeting($greetingId,false,'eq',true);
            // Вызываем функцию, которая вернёт элементы по номеру шаблона
            //   Поидее если у нас $printRecom = true, то templateId должен быть задан тоже
            $changedElements = MedcardElementForPatient::model()->findGreetingTemplate($greetingId,$templateId);
        }

        //var_dump($changedElements );
        //exit();

        //  foreach ($changedElements as $oneEl)
        //   {
        //        var_dump($oneEl['value'] .' '.$oneEl['element_id']);
        //      }
//exit();
        if(count($changedElements) == 0) {
            // Единичная печать
            if($greetingIn === false) {
                //var_dump($changedElements);
                //exit();

                exit('Во время этого приёма не было произведено никаких изменений!');
            } else {
                return array();
            }
        }

        // Создадим виджет
        $categorieWidget = $this->createWidget('application.modules.doctors.components.widgets.CategorieViewWidget');

        //var_dump($changedElements);
        //exit();

        // Запихнём виджету те элементы, которые мы вытащили по приёму
        $categorieWidget->setHistoryElements($changedElements);

        // Провернём как в мясорубке элементы в этом виджете
        $categorieWidget->makeTree('getTreeNodePrint');
        $categorieWidget->sortTree();
        // Теперь поделим категории
        $categorieWidget->divideTreebyCats();

        $sortedElements = $categorieWidget->dividedCats;

        // Вытащим диагнозы
        //var_dump($greetingId);
        //exit();

        $pd = PatientDiagnosis::model()->findDiagnosis($greetingId, 0);
        $sd = PatientDiagnosis::model()->findDiagnosis($greetingId, 1);
        $cd = PatientDiagnosis::model()->findDiagnosis($greetingId, 2);
        $cpd = ClinicalPatientDiagnosis::model()->findDiagnosis($greetingId, 0);
        $csd = ClinicalPatientDiagnosis::model()->findDiagnosis($greetingId, 1);
        $noteDiagnosis = $greeting['note'];

        //var_dump($cd);
        //exit();

        // Соберём их в об'ект
        $diagnosises = array(
            'primary' => $pd,
            'secondary' => $sd,
            'clinicalPrimary' => $cpd,
            'clinicalSecondary' => $csd,
            'complicating' => $cd,
            'noteGreeting' => $noteDiagnosis
        );

        //var_dump($diagnosises );
        //exit();

        //var_dump($sortedElements);
        //exit();
        if($greetingIn === false) {
            if(!$returnResult) {
                $mPDF = Yii::app()->ePdf->mpdf('', 'A5-L');

                if ($printRecom)
                {
                    $mPDF = Yii::app()->ePdf->mpdf('', 'A5-L', 0,'',8,8,8,8,8,8);
                }

                $stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css').'/print.css');
                $mPDF->WriteHTML($stylesheet, 1);

                $stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css').'/print.less');
                $mPDF->WriteHTML($stylesheet, 1);


                $stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css').'/paper.less');
                $mPDF->WriteHTML($stylesheet, 1);
                $htmlForPdf = '';

                // Если печатаем рекомендации - печатаем их по-другому, в другой совершенно форме:
                if ($printRecom)
                {
                    $htmlForPdf =
                        $this->render('recommendationPdf', array(
                            'templates' => $sortedElements,
                            'greeting' => $greetingInfo,
                            'diagnosises' => $diagnosises,
                            'enterprise' => $enterprise
                        ), true);
                }
                else
                {
                    $htmlForPdf =
                        $this->render('greeting', array(
                            'templates' => $sortedElements,
                            'greeting' => $greetingInfo,
                            'diagnosises' => $diagnosises
                        ), true);
                }

                $mPDF->WriteHTML(
                    $htmlForPdf
                );

                ob_end_clean();
                $this->render('greetingpdf', array(
                    'pdfContent' => $mPDF->Output()
                ));
            } else {
                return array(
                    'templates' => $sortedElements,
                    'greeting' => $greetingInfo,
                    'diagnosises' => $diagnosises
                );
            }
        } else {
            return array(
                'templates' => $sortedElements,
                'greeting' => $greetingInfo,
                'diagnosises' => $diagnosises
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

        $mPDF = Yii::app()->ePdf->mpdf();
        $mPDF = Yii::app()->ePdf->mpdf('', 'A5-L');

        $stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css').'/print.css');
        $mPDF->WriteHTML($stylesheet, 1);

        $stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css').'/print.less');
        $mPDF->WriteHTML($stylesheet, 1);

        $stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css').'/paper.less');
        $mPDF->WriteHTML($stylesheet, 1);

        $mPDF->WriteHTML($this->render('massprintonelist',
            array(
                'greetings' => $response,
                'notPrintButton' => true
            )
            , true));

        $this->render('massgreetingspdf', array(
            'pdfContent' => $mPDF->Output()
        ));
    }



    // Получить данные для вьюхи
    public function actionMakePrintListView() {
        $patients = CJSON::decode($_GET['patients']);
        $doctors = CJSON::decode($_GET['doctors']);

        $resultArr = $this->makePrintListData($doctors, $patients);
        echo CJSON::encode(array('success' => 'true',
                                 'data' => $resultArr));
    }

    private function makePrintListData($doctors, $patients) {
        $numPatients = count($patients);
        $numDoctors = count($doctors);
        $resultArr = array();
        for($i = 0; $i < $numPatients; $i++) {
            for($j = 0; $j < $numDoctors; $j++) {
                // Теперь получаем все приёмы по врачу, пациенту и дате

                // Вот тут надо сконструировать фильтр для пары врач-пациент
                $filterObject = array(
                    'groupOp' => 'AND',
                    'rules' => array()
                );

                array_push($filterObject['rules'],
                    array(
                     'field' => 'patients_ids',
                     'op' => 'in',
                     'data' => array($patients[$i])
                    )
                );

                array_push($filterObject['rules'],
                    array(
                        'field' => 'doctors_ids',
                        'op' => 'in',
                        'data' => array($doctors[$j])
                    )
                );

                if(isset($_GET['date']) && trim($_GET['date']) != '') {
                   // $greetings = SheduleByDay::model()->getGreetingsPerQrit($patients[$i], $doctors[$j], $_GET['date']);
                    array_push($filterObject['rules'],
                        array(
                            'field' => 'patient_day',
                            'op' => 'eq',
                            'data' => $_GET['date']
                        )
                    );

                }
                /*else {
                  //  $greetings = SheduleByDay::model()->getGreetingsPerQrit($patients[$i], $doctors[$j]);


                }*/

                $greetings = SheduleByDay::model()->getGreetingsPerQrit($filterObject);

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

       // var_dump($resultArr);
       // exit();
        return $resultArr;
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