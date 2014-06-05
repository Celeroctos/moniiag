<?php
class SheduleController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';

    public function actionView() {
        $ward = new Ward();
        $wardsResult = $ward->getRows(false, 'name', 'asc');
        $wardsList = array('-1' => 'Нет');
        foreach($wardsResult as $key => $value) {
            $wardsList[$value['id']] = $value['name'];
        }

        // Список должностей
        $post = new Post();
        $postsResult = $post->getRows(false, 'name', 'asc');
        $postsList = array('-1' => 'Нет');
        foreach($postsResult as $key => $value) {
            $postsList[$value['id']] = $value['name'];
        }

        // Модель формы для модицикации расписания
        $formModel = new FormSheduleAdd();

        // Список кабинетов
        $cabinet = new Cabinet();
        $cabinetResult = $cabinet->getRows(false, 'cab_number', 'asc');
        $cabinetList = array();
        foreach($cabinetResult as $key => $value) {
            $cabinetList[$value['id']] = $value['cab_number'].' - '.$value['description'].', '.$value['ward'].' отделение, '.$value['enterprise'];
        }

        $daysExp = $this->getExpDays(true);

		/*Получим список выходных дней по поликилинике*/
		$restDaysDb = SheduleRest::model()->findAll();
		$restDays = array();
		
		foreach ($restDaysDb as $oneDay)
		{
			$restDays[] = $oneDay['day'];
		}
		
		$formModel->weekEnds = CJSON::encode($restDays);
		
		// var_dump($restDays );
		// exit();

        $this->render('index', array(
            'wardsList' => $wardsList,
            'postsList' => $postsList,
            'model' => $formModel,
            'daysExp' => $daysExp,
            'cabinetList' => $cabinetList
        ));
    }


	public function actionGetWrittenPatientsEdit()
 	{
        $result = $this->getPatientsWrittenEdit();
        // В поле рекордс добавляем информацию о пациентах
        $this->getPatientsInfoToGreetings($result['rows']);
        $greetingsJSON = CJSON::encode($result);
        echo $greetingsJSON;
 	}

    // Получить пациентов, записанных на данного врача (голые строки)
    // Новая часть
    private function getPatientsWritten()
    {
        try {

            // Прочитаем параметры
            if (!isset($_GET['rows']))
            {
                $rows = false;
            }
            else
            {
                $rows = $_GET['rows'];
            }

            if (!isset ($_GET['page']))
            {
                $page = false;
            }
            else
            {
                $page = $_GET['page'];
            }

            if (!isset($_GET['sidx']))
            {
                $sidx = false;
            }
            else
            {
                $sidx = $_GET['sidx'];
            }

            if (!isset($_GET['sord']))
            {
                $sord = false;
            }
            else
            {
                $sord = $_GET['sord'];
            }


            if(isset($_GET['filters']) && trim($_GET['filters']) != '')
            {
                $filters = CJSON::decode($_GET['filters']);
            }
            else
            {
                $filters = false;
            }

            $dayBegin = $_GET['begin'];
            $dayEnd = $_GET['end'];
            //$doctorId= $_GET['doctor_id'];
            $doctors = array();

            // Инициализируем массив докторов


            if (isset($_GET['doctorsIds']))
            {

                //var_dump($_GET['doctorsIds']);
                //exit();

                if (is_array($_GET['doctorsIds']))
                {
                    $doctorsArr = $_GET['doctorsIds'];
                }
                else
                {
                    $doctorsArr = CJSON::decode($_GET['doctorsIds']);
                }




                if (is_array($doctorsArr))
                {
                    $doctors = $doctorsArr;
                }
                else
                {
                    $doctors[] = $doctorsArr;
                }
            }
            else
            {
                $doctors[] = $_GET['doctor_id'];
            }

            $model = new SheduleByDay();
            $num = $model->getRangePatientsRows($filters, $dayBegin, $dayEnd,$doctors);
            //var_dump($num);
            //exit();


            $totalPages = 0;
            $start = 0;
            if ($rows)
            {
                $totalPages = ceil(count($num) / $rows);
                $start = $page * $rows - $rows;
            }


            $greetings = $model->getRangePatientsRows($filters, $dayBegin, $dayEnd,$doctors, $sidx, $sord, $start, $rows);

            //var_dump($greetings);
            //exit();
            // Приведём дату в приличный вид
            foreach($greetings as &$element) {


                $greetingDateArr= explode('-', $element['patient_day']);
                $element['patient_date'] =	$greetingDateArr[2].'.'
                    .$greetingDateArr[1].'.'
                    .$greetingDateArr[0].' '.$element['patient_time'];


                $element['doctor_fio'] = $element['last_name'];

                if ($element['first_name']!='')
                {
                    $element['doctor_fio'] .= (' '.mb_substr($element['first_name'],0,1,'utf-8').'.');
                }

                if ($element['middle_name']!='')
                {
                    $element['doctor_fio'] .= (' '.mb_substr($element['middle_name'],0,1,'utf-8'). '.');
                }


                //$element['unwrite'] = '\<Test'.(string)$element['id'];



                $element['unwrite'] = '<a class="unwrite-link" href="#'.(string)$element['id'].'">'.
                    '<span class="glyphicon glyphicon-remove" title="Снять пациента с записи"></span>'.
                    '</a>';

                //var_dump($element['unwrite']);
                //exit();
                //var_dump($element);
            }

          //  var_dump($greetings);
          //  exit();
            return
                array(	'rows' => $greetings,
                    'total' => $totalPages,
                    'records' => count($num))
            ;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    private function getPatientsInfoToGreetings(&$arrayOfGreetings)
    {
        // Нужно прочитать для каждого приёма ФИО пациента и его контакты пациенты
        //    Цимес в том, что ФИО и контакты пациента могут быть либо в таблице опосредованных пациентов, либо
        //     из обычных


        // Перебираем приёмы
        //    Для каждого приёма смотрим
        //    Опосредованный ли он
        //   Если да - то читаем из таблицы опосредованных пацентов

        // Ассоциативные массивы [Id приёма] => Индекс в выборке
        $mediateAssociation = array();
        $directAssociation = array();

        // Информация о приёмах опосредованных и обычных
        $mediatesInfo = null;
        $directsInfo = null;

        $mediateIds = array();
        $directIds = array();

        // Разделим ИД приёмов
        foreach ($arrayOfGreetings as $oneGreeting)
        {
            if ($oneGreeting['mediate_id']=='')
            {
                array_push( $directIds,$oneGreeting['id']);
            }
            else
            {
                array_push( $mediateIds ,$oneGreeting['id']);
            }
        }


        // Прочитаем инфу о приёмах по отдельности
        if (count($mediateIds)!=0)
        {
            $mediatesInfo = SheduleByDay::getMediateGreetingsInfo($mediateIds);
        }

        if (count($directIds)!=0)
        {
            $directsInfo = SheduleByDay::getDirectGreetingsInfo($directIds);
        }
        // Сделаем массивы ключей
        for($i=0;$i<count($mediatesInfo );$i++)
        {
            $mediateAssociation[$mediatesInfo [$i]['id']] = $i;
        }


        for($i=0;$i<count($directsInfo );$i++)
        {
            $directAssociation[$directsInfo [$i]['id']] = $i;
        }

        // Перебираем массив приёмов и добавляем в каждый элемент инфу о приёме в зависимости от того, опосредованный он или нет
        foreach ($arrayOfGreetings as &$oneGreeting)
        {
            // Определён ли этот ид в обычных приёмах
            if (isset  (    $directAssociation[$oneGreeting['id']]   )  )
            {
                // Приём обычный
                $infoIndex =$directAssociation[$oneGreeting['id']];
                $oneGreeting['fio'] = $directsInfo[$infoIndex ]['fio'];
                $oneGreeting['contact'] = $directsInfo[$infoIndex ]['contact'];

            }
            else
            {
                // Приём опосредованный
                $infoIndex =$mediateAssociation[$oneGreeting['id']];
                $oneGreeting['fio'] = $mediatesInfo[$infoIndex ]['fio'];
                $oneGreeting['contact'] = $mediatesInfo[$infoIndex ]['contact'];
            }
        }
    }

    // Получить пациентов, записанных на данного врача при редактировании расписания (голые строки из базы)
    // Новая часть
    private function getPatientsWrittenEdit()
    {
        try {

            // Прочитываем параметры
            if (!isset($_GET['rows']))
            {
                $rows = false;
            }
            else
            {
                $rows = $_GET['rows'];
            }

            if (!isset ($_GET['page']))
            {
                $page = false;
            }
            else
            {
                $page = $_GET['page'];
            }

            if (!isset($_GET['sidx']))
            {
                $sidx = false;
            }
            else
            {
                $sidx = $_GET['sidx'];
            }

            if (!isset($_GET['sord']))
            {
                $sord = false;
            }
            else
            {
                $sord = $_GET['sord'];
            }


            if(isset($_GET['filters']) && trim($_GET['filters']) != '')
            {
                $filters = CJSON::decode($_GET['filters']);
            }
            else
            {
                $filters = false;
            }

            $dayBegin = $_GET['date_begin'];
            $dayEnd = $_GET['date_end'];
            $doctorId= $_GET['doctor_id'];
            $times = CJSON::decode($_GET['times']);
            $sheduleId = $_GET['shedule_id'];
            $idGreetingToCancel = array();
            //------------------
            // Дальше нужно собрать id тех приёмов, которые нужно отменить
            //------------------
            $oldShedule = SheduleSettedBe::model()->find('id = :id', array(':id'=>$sheduleId));

            // Выбираем приёмы, которые попадали раньше в промежуток
            $oldGreetings =  SheduleByDay::model()->findAll('doctor_id = :doctor_id AND patient_day > :date_begin AND patient_day < :date_end',
                array(':doctor_id' => $doctorId,':date_begin' => $oldShedule['date_begin'],
                    ':date_end' => $oldShedule['date_end']
                )
            );
            // Выбираем приёмы, которые попадают в промежуток теперь
            $newGreetings = SheduleByDay::model()->findAll('doctor_id = :doctor_id AND patient_day > :date_begin AND patient_day < :date_end',
                array(':doctor_id' => $doctorId,':date_begin' => $dayBegin,
                    ':date_end' => $dayEnd
                )
            );
            $newGreetingsCount = count($newGreetings );
            $oldGreetingsCount = count($oldGreetings );

            foreach($newGreetings as $oneNewGreeting)
            {
                $wasFound = false;
                foreach($oldGreetings as $oneOldGreeting)
                {
                    if ($oneNewGreeting['id']==$oneOldGreeting['id'])
                    {
                        $wasFound = true;
                        break;
                    }
                }
                // Новый приём не нашли в старых.
                // Это значит, что после изменения расписания приём оказался в даном периоде, хотя раньше там не был
                if (!$wasFound)
                {
                    //var_dump($oneNewGreeting['id']);
                    $idGreetingToCancel[] = $oneNewGreeting['id'];
                    /*
                    $arrayStatusChanged = 1;
                    break;
                    */
                }
            }

            foreach($oldGreetings as $oneOldGreeting)
            {
                $wasFound = false;
                foreach($newGreetings as $oneNewGreeting)
                {
                    if ($oneNewGreeting['id']==$oneOldGreeting['id'])
                    {
                        $wasFound = true;
                        break;
                    }
                }
                // Не нашли старый приём в новых. Это значит, что приём "вывалился"
                if (!$wasFound)
                {
                    //var_dump($oneNewGreeting['id']);
                    $idGreetingToCancel[] = $oneOldGreeting['id'];
                    //$arrayStatusChanged = 1;
                    //break;
                }
            }

            //exit();
            // Перебираем старые приёмы
            foreach($oldGreetings as $oneOldGreeting)
            {
                if (!in_array($oneOldGreeting['id'],$idGreetingToCancel))
                {


                    $weekday = date('w', strtotime($oneOldGreeting['patient_day']));
                    // Надо проверить - попадает ли patient_time в промежуток между
                    //    началом приёма в данный день недели

                    // Если в этот день приёма нет (хотя приём записан)
                    if ($times['timesBegin'][$weekday]=='' ||$times['timesEnd'][$weekday]=='')
                    {
                        //$arrayStatusChanged = 1;
                        //break;
                        $idGreetingToCancel[] = $oneOldGreeting['id'];
                    }

                    // Если время приёма не попадает в новый промежуток времени
                    if (!(strtotime($oneOldGreeting['patient_time'])>strtotime($times['timesBegin'][$weekday]))
                        &&
                        (strtotime($oneOldGreeting['patient_time'])<strtotime($times['timesEnd'][$weekday])))
                    {
                        //$arrayStatusChanged = 1;
                        //break;
                        $idGreetingToCancel[] = $oneOldGreeting['id'];
                    }
                }
            }

            // В данной точке имеем в массиве $idGreetingToCancel перечисление ИД приёмов,
            //   которые надо отменить, чтобы изменить расписание
            // Теперь надо эти приёмы по-нормальному выбрать

            $idsString = ''; // Строка, которая содержит распарсенное перечисление id, которых надо выбрать из базы

            // Склеим идшники
            foreach($idGreetingToCancel as $oneId)
            {
                if ($idsString != '')
                {
                    $idsString = $idsString.',';
                }
                $idsString =$idsString.((string)$oneId);

            }

            $model = new SheduleByDay();
            $num = $model->getGreetingsByIds($filters, $idsString);



            $totalPages = 0;
            $start = 0;
            if ($rows)
            {
                $totalPages = ceil(count($num) / $rows);
                $start = $page * $rows - $rows;
            }

            $greetings = $model->getGreetingsByIds($filters, $idsString, $sidx, $sord, $start, $rows);

            // Приведём дату в приличный вид и запишем ссылку для отписывания
            foreach($greetings as &$element) {
                $greetingDateArr= explode('-', $element['patient_day']);
                $element['patient_date'] =	$greetingDateArr[2].'.'
                    .$greetingDateArr[1].'.'
                    .$greetingDateArr[0].' '.$element['patient_time'];

                $element['doctor_fio'] = $element['last_name'];

                if ($element['first_name']!='')
                {
                    $element['doctor_fio'] .= (' '.mb_substr($element['first_name'],0,1,'utf-8').'.');
                }

                if ($element['middle_name']!='')
                {
                    $element['doctor_fio'] .= (' '.mb_substr($element['middle_name'],0,1,'utf-8'). '.');
                }

                $element['unwrite'] = '<a class="unwrite-link" href="#' .(string) $element['id'] . '">' .
                    '<span class="glyphicon glyphicon-remove" title="Снять пациента с записи"></span>' .
                    '</a>';
            }
            return
                array(	'rows' => $greetings,
                    'total' => $totalPages,
                    'records' => count($num));

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }




    public function actionGetWrittenPatients()
 	{
        $result = $this->getPatientsWritten();
        // В поле рекордс добавляем информацию о пациентах
        $this->getPatientsInfoToGreetings($result['rows']);
        $greetingsJSON = CJSON::encode($result);
        echo $greetingsJSON;
 	}
 
 	// Возвращает количество пацентов, записанных на данный промежуток времени
 	//    у данного врача
 	public function actionIsGreeting()
 	{
        $result = $this->getPatientsWritten();
            echo CJSON::encode(
                array(
                    'success'=> true,
                    'data'=> $result['records']
                )
            );
    }

    public function actionIsGreetingEdit()
    {
        $result = $this->getPatientsWrittenEdit();
        echo CJSON::encode(
            array(
                'success'=> true,
                'data'=> $result['records']
            )
        );
    }

	// Получение смен врачей
	public function actionGetShiftsEmployee() {
		try {
			$rows = $_GET['rows'];
			$page = $_GET['page'];
			$sidx = $_GET['sidx'];
			$sord = $_GET['sord'];
			/*var_dump($_POST);
			var_dump($_GET);
			exit();
			*/
			if (isset($_GET['doctorId']))
			{
				$doctorId = $_GET['doctorId'];
				//var_dump($doctorId );
				//exit();
				// Фильтры поиска
				if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
					$filters = CJSON::decode($_GET['filters']);
				} else {
					$filters = false;
				}
				
				$model = new SheduleSettedBe();
				$num = $model->getRows($filters, $doctorId);

				$totalPages = ceil(count($num) / $rows);
				$start = $page * $rows - $rows;

				$shifts = $model->getRows($filters, $doctorId, $sidx, $sord, $start, $rows);
				
				// Приведём дату в приличный вид
				foreach($shifts as &$element) {
					$beginArr = explode('-', $element['date_begin']);
					$endArr = explode('-', $element['date_end']);
					$element['date_begin'] = $beginArr[2].'.'.$beginArr[1].'.'.$beginArr[0];
					$element['date_end'] = $endArr[2].'.'.$endArr[1].'.'.$endArr[0];
				}
				echo CJSON::encode(
					array('rows' => $shifts,
							'total' => $totalPages,
							'records' => count($num))
						);
			}
			else
			{
				echo CJSON::encode(array('success' => 'false',
					'msg' => 'Ошибка - не задан сотрудник'));
			}
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

    // Дни-исключения
    public function getExpDays($createFirst = false) {
        $result = array();
        if($createFirst) {
            $result[] = new FormSheduleExpAdd();
        } else {
            // Здесь нужно сделать выборку тех дней-исключений, которые уже есть в базе..
        }
        return $result;
    }

	// Возвращает смену для сотрудна по id
	public function actionGetOne() {		
		$result = array();
		if (isset($_GET['id']))
		{
			
			$shedule = SheduleSettedBe::model()->findByPk($_GET['id']);
			$result['date_begin'] = $shedule['date_begin'];
			$result['date_end'] = $shedule['date_end'];
			$result['employee_id'] = $shedule['employee_id'];
			$result['id'] = $shedule['id'];
			
			// Достаём все дни для смены
			$days = SheduleSetted::model()->findAll('date_id = :date_id', array(':date_id' => $shedule->id));
			foreach($days as $oneDay)
			{
				$result['timeBegin'.$oneDay['weekday']] = $oneDay['time_begin'];
				$result['timeEnd'.$oneDay['weekday']] = $oneDay['time_end'];
				$result['cabinet'.$oneDay['weekday']] = $oneDay['cabinet_id'];
			}
		
		}
		
		echo CJSON::encode(array('success' => true,
			'data' => $result ));
		
	}

    public function actionAddEdit() {
        $model = new FormSheduleAdd();
        if(isset($_POST['FormSheduleAdd'])) {
            $model->attributes = $_POST['FormSheduleAdd'];
            $this->addEditModelShedule($model);
            echo CJSON::encode(array('success' => 'true',
                                     'msg' => 'Операция успешно проведена, расписание сохранено'));
        }
    }

    public function addEditModelShedule($model) {
		// Проверяем, правильно ли введена дата начала и дата конца
		if (!($model->validate(array('dateBegin')))||!($model->validate(array('dateEnd'))) ){
				echo CJSON::encode(array('success' => 'false',
						'errors' => 'Не заполнена дата начала или дата конца действия расписания' ));
				exit();
			}
		
		// Сохраняем сначала смену
		$sheduleSettedBeModel = new SheduleSettedBe();
		// Найдём смену, если задан её id
		//var_dump($model->sheduleEmployeeId);
		if ($model->sheduleEmployeeId != '' &&$model->sheduleEmployeeId !=  null)
		{
			$sheduleSettedBeModel  = SheduleSettedBe::model()->find('id = :id', array(':id' => $model->sheduleEmployeeId));
			//$sheduleSettedBeModel = $shedulesFromBase [0];
		}
		$sheduleSettedBeModel->date_begin = $model->dateBegin;
        $sheduleSettedBeModel->date_end = $model->dateEnd;
		
		
		$sheduleSettedBeModel->employee_id = $model->doctorId;
		if(!$sheduleSettedBeModel->save()) {
			echo CJSON::encode(array('success' => 'false',
				'errors' => 'Не cмогу добавить элемент расписания в базу!'));
			exit();
		}
		
		// Вот тут делаем следующее: 
		//  1. Перебираем расписания
		//  2. Определяем - нужно ли изменить их дату начала и дату конца
		//  (попадает дата начала и дата конца на даты нового расписания)
		$existingShedules = SheduleSettedBe::model()->findAll('employee_id = :doctor_id', array(':doctor_id' => $model->doctorId));
		
		// Переберём смены
		foreach ($existingShedules as $oneShedule)
		{
			// Если смена не та, которую мы только что сохранили
			if ($oneShedule['id']!=$sheduleSettedBeModel['id'])
			{
				if ((strtotime($oneShedule['date_begin'])>=strtotime($sheduleSettedBeModel->date_begin))
					&&(strtotime($oneShedule['date_end'])<=strtotime($sheduleSettedBeModel->date_end)))
				{
					// Новое расписание полностью перекрывает старое. Старое поидее надо удалить
					$dateId =  $oneShedule['id'];
					$oneShedule->delete();
					// УБиваем старое расписание для данной смены
					SheduleSetted::model()->deleteAll('date_id = :date_id', array(
						':date_id' => $dateId
						));
					continue;	
				}
				
				if ((strtotime($oneShedule['date_begin'])<strtotime($sheduleSettedBeModel->date_begin))
					&&(strtotime($oneShedule['date_end'])>strtotime($sheduleSettedBeModel->date_begin))
					&&(strtotime($oneShedule['date_end'])<strtotime($sheduleSettedBeModel->date_end)))
				{
					
					// Новое расписание залезает на хвост старого. У старого надо изменить дату конца на дату, предшествующую
					//   началу нового
					$oneShedule['date_end'] = date("Y-m-d",strtotime($sheduleSettedBeModel->date_begin)-86400);
					$result = $oneShedule->save();
					continue;	
				}
				
				if ((strtotime($oneShedule['date_end'])>strtotime($sheduleSettedBeModel->date_end))
					&&(strtotime($oneShedule['date_begin'])>strtotime($sheduleSettedBeModel->date_begin))
					&&(strtotime($oneShedule['date_begin'])<strtotime($sheduleSettedBeModel->date_end)))
				{
					// Новое расписание залезает на голову старого. У старого надо изменить дату начала на дату после 
					//   конца нового
					$oneShedule['date_begin'] = date("Y-m-d",strtotime($sheduleSettedBeModel->date_end)+86400);
					$result = $oneShedule->save();
					continue;
				}
				
				if ((strtotime($oneShedule['date_begin'])<strtotime($sheduleSettedBeModel->date_begin))
					&&(strtotime($oneShedule['date_end'])>strtotime($sheduleSettedBeModel->date_end)))
				{
					// Новое раписание находится посередине старого. Старое расписание надо разбить на две части
					// Алгоритм такой.
					// 1. Сохраняем дату конца старого расписания
					// 2. Дату конца старого расписания меняем как дата начала нового - 1 день
					// 3. Вставляем новое расписание, которое ничем не будет отличаться 
					//   от старого кроме того, что у него дата начала будет равняться дате конца нового расписания + 1 день
					
					// 1
					$oldEndDate = $oneShedule['date_end'];
					
					// 2
					$oneShedule['date_end'] = date("Y-m-d",strtotime($sheduleSettedBeModel->date_begin)-86400);
					$result = $oneShedule->save();
					
					// 3
					$addingShedule = new SheduleSettedBe();
					$addingShedule->date_begin = date("Y-m-d",strtotime($sheduleSettedBeModel->date_end)+86400);
					$addingShedule->date_end = 	$oldEndDate;
					$addingShedule->employee_id = $model->doctorId;
					
					$addingShedule->save();
					
					// Теперь копируем дни недели
					$days = SheduleSetted::model()->findAll('date_id = :date_id', array(
						':date_id' => $oneShedule['id']
						));
					foreach($days as $oneDay)
					{
						$cloneDay = new SheduleSetted();
						$cloneDay ->cabinet_id = $oneDay['cabinet_id'];
						$cloneDay ->employee_id = $oneDay['employee_id'];
						$cloneDay ->weekday = $oneDay['weekday'];
						$cloneDay ->time_begin = $oneDay['time_begin'];
						$cloneDay ->time_end = $oneDay['time_end'];
						$cloneDay ->type = 0; // Обычное расписание
						$cloneDay ->date_id = $addingShedule['id'];
						$cloneDay ->save();
						
					}
					
					
					continue;	
				}
				
			}
		}
		
		
		// УБиваем старое расписание для данной смены
		SheduleSetted::model()->deleteAll('date_id = :date_id', array(
			':date_id' => $sheduleSettedBeModel->id
			));
	
		// Провалидируем время для каждого дня
		for($i = 0; $i < 7; $i++)
		{
			if(($model->validate(array('timeBegin'.$i)) && !$model->validate(array('timeEnd'.$i))) ||
				(!$model->validate(array('timeBegin'.$i)) && $model->validate(array('timeEnd'.$i)))) {
					echo CJSON::encode(array('success' => 'false',
							'errors' => $model->errors));
					exit();
				}
		}
		
		// Запишем в базу данных
		for($i = 0; $i < 7; $i++)
		{
				$timeBegin = 'timeBegin'.$i;
				$timeEnd = 'timeEnd'.$i;
				
				// Если время начала или время конца - пусто, то значит, день для врача об'явлен выходным
				if ((($model->$timeBegin=='')||($model->$timeBegin==null) )
					|| (($model->$timeEnd=='')||($model->$timeEnd==null) ))
						continue;
				
				$day = new SheduleSetted();
				$cabinet = 'cabinet'.$i;
				$day->cabinet_id = $model->$cabinet;
				$day->employee_id = $model->doctorId;
				$day->weekday = $i;
				$timeBegin = 'timeBegin'.$i;
				$day->time_begin = $model->$timeBegin;
				$timeEnd = 'timeEnd'.$i;
				$day->time_end = $model->$timeEnd;
				$day->type = 0; // Обычное расписание
				$day->date_id = $sheduleSettedBeModel->id;
				
				
				if(!$day->save()) {
					echo CJSON::encode(array('success' => 'false',
							'errors' => 'Не могу добавить элемент расписания в базу!'));
					exit();
				}				
		}
    }

	// Поидее нужно будет переписать
	public function actionDelete() {
			if (isset($_GET['id']))
			{
				 try {
					// Сначала удаляем расписание по дням для дней недели
					SheduleSetted::model()->deleteAll('date_id = :date_id', array(
							':date_id' => $_GET['id']
							));
				
					// Удаляем смену
					SheduleSettedBe::model()->deleteAll('id = :id', array(
							':id' => $_GET['id']
							));
					echo CJSON::encode(array('success' => 'true',
							'text' => 'Расписание успешно удалено'));
				}
				catch (Exception $e)
				{
					
				}
					
			}
	}

    public function addEditModelSheduleExp($model) {
        if($model->id != null) {
            $day = SheduleSetted::model()->find('id = :id', array(':id' => $model->id));
            if($day == null) {
                $day = new SheduleSetted();
            }
        } else {
            $day = new SheduleSetted();
        }

        $day->cabinet_id = $model->cabinet;
        $day->employee_id = $model->doctorId;
        $day->time_begin = $model->timeBegin;
        $day->time_end = $model->timeEnd;
        $day->day = $model->day;
        $day->type = 1; // День-исключение

        if(!$day->save()) {
            echo CJSON::encode(array('success' => 'false',
                                     'errors' => 'Не могу добавить элемент расписания в базу!'));
            exit();
        }
    }

    public function actionAddEditExps() {
        if(isset($_POST['FormSheduleExpAdd'])) {
            foreach($_POST['FormSheduleExpAdd'] as $key => $item) {
                $model = new FormSheduleExpAdd();
                $model->attributes = $item;
                if(!$model->validate()) {
                    // Типа, "я пропускаю это или удаляю"
                    if(trim($model->day) == '' && trim($model->timeBegin) == '' && trim($model->timeEnd) == '') {
                        // т.е. это удаление строки
                        if(trim($model->id) != '' && trim($model->id) != null) {
                            $m =  SheduleSetted::model()->find('id = :id', array(':id' => $model->id));
                            if($m != null) {
                                $m->delete();
                            }
                        }
                        continue;
                    } else { // Хотя бы одно поле не удалено из строки - ошибка!
                        echo CJSON::encode(array('success' => 'false',
                                                 'errors' => $model->errors));
                        exit();
                    }
                } else {
                    $this->addEditModelSheduleExp($model);
                }
            }
        }
        echo CJSON::encode(array('success' => 'true',
                                 'msg' => 'Операция успешно проведена, расписание сохранено'));
    }

    // Получение раписания для конкретного врача
    public function actionGet($id) {
        $rows = SheduleSetted::model()->findAll('employee_id = :employee_id', array(':employee_id' => $id));
        $resultArr = array(
            'data' => array()
        );
        if(count($rows) > 0) {
            $sheduleSettedBeModel = SheduleSettedBe::model()->find('id = :id', array(':id' => $rows[0]->date_id));
            if($sheduleSettedBeModel != null) {
                $resultArr['dateBegin'] = $sheduleSettedBeModel->date_begin;
                $resultArr['dateEnd'] = $sheduleSettedBeModel->date_end;
            } else {
                $resultArr['dateBegin'] = '';
                $resultArr['dateEnd'] = '';
            }
        }
        foreach($rows as $row) {
            $row->time_begin = substr($row->time_begin, 0, strrpos($row->time_begin, ':'));
            $row->time_end = substr($row->time_end, 0, strrpos($row->time_end, ':'));
            $resultArr['data'][] = array(
                'timeBegin' => $row->time_begin,
                'timeEnd' => $row->time_end,
                'cabinetId' => $row->cabinet_id,
                'employeeId' => $row->employee_id,
                'weekday' => $row->weekday,
                'day' => $row->day,
                'type' => $row->type,
                'id' => $row->id
            );
        }
        echo CJSON::encode(array('success' => 'true',
                                 'data' => $resultArr));
    }

    // Просмотр календаря выходных дней
    public function actionViewRest() {
        $restModel = new FormRestDaysEdit();
        $restDays = SheduleRest::model()->findAll();
        $restDaysResponse = array();
        $restDaysValues = array();

        // Выбираем докторов с табельным номером
        $doctorObject = new Doctor();
        $doctors = $doctorObject->getAll();
        //var_dump($doctors);
        //exit();


        if(!isset($_GET['date'])) {
            $dateBegin = date('Y-n-j');
        } else {
            $dateBegin = $_GET['date'];
        }
        foreach($restDays as $day) {
            array_push($restDaysValues, $day['day']);
            //var_dump ($day['day']);
            $restDaysResponse[$day['day']] = array('selected' => 'selected');
        }
        //var_dump($restDaysValues);
        //exit();
        $restModel->restDays = $restDaysValues;
        $parts = explode('-', $dateBegin);
        //var_dump($doctors);
        //exit();
        $this->render('rest', array(
            'model' => $restModel,
            'selectedDaysJson' => CJSON::encode($restDaysResponse),
            'selectedDays' => $restDaysResponse,
            'restCalendars' => CJSON::encode($this->getRestDays($dateBegin)),
            'firstDay' => date('w', strtotime($dateBegin)),
            'year' => $parts[0],
            'doctors' => $doctors,
            'displayPrev' => date('Y') < $parts[0],
            'restDays' => array(1 => 'Понедельник',
                2 => 'Вторник',
                3 => 'Среда',
                4 => 'Четверг',
                5 => 'Пятница',
                6 => 'Суббота',
                0 => 'Воскресенье')
        ));
    }

    // Редактирование календаря выходных дней
    public function actionRestEdit() {
        $model = new FormRestDaysEdit();
        if(isset($_POST['FormRestDaysEdit'])) {
            $model->attributes = $_POST['FormRestDaysEdit'];
            SheduleRest::model()->deleteAll();
            foreach($model->restDays as $day) {
                $sheduleRest = new SheduleRest();
                $sheduleRest->day = $day;
                if(!$sheduleRest->save()) {
                    echo CJSON::encode(array('success' => false,
                        'msg' => 'Не могу сохранить выходной день!'));
                }
            }
            echo CJSON::encode(array('success' => true,
                'msg' => 'Выходные дни успешно сохранены.'));
        }
    }

    private function getRestDays($dateBegin) {
        $parts = explode('-', $dateBegin);
        $dateEnd = ($parts[0] + 1).'-'.$parts[1].'-'.$parts[2];
        $responseDb = SheduleRestDay::model()->findAll('t.date >= :dateBegin AND t.date < :dateEnd', array(':dateBegin' => $dateBegin, ':dateEnd' => $dateEnd));
        $response = array();
        // Делим всю выборку на 12 месяцев
        foreach($responseDb as $day) {
            $month = date('n', strtotime($day['date']));
            if(!isset($response[$month - 1])) {
                $response[$month - 1] = array();
            }
            // Теперь смотрим, какие даты подгоняются под этот месяц
            $response[$month - 1][] = $day;
        }
        return $response;
    }

    public function actionSaveRestDays()
    {
        // Получим данные для записи (они были заэнкожены, чтобы было проще передать)
        $dataToWrite = CJSON::decode($_POST['calendarData']);
        // Теперь надо убить все строки с датами, которые указаны в пришедших данных

        // Читаем ключи ассоциативного массива
        $dateToDelete = array();
        foreach ($dataToWrite as $key => $oneDate)
        {
            $dateToDelete[] = $key;
        }
        // Удаляем
        SheduleRestDay::deleteDates($dateToDelete);
        // А теперь пишем обратно
        SheduleRestDay::writeAllRestDays($dataToWrite);

        echo CJSON::encode(array('success' => true,
            'data' => array()));
    }


    public function actionGetHolidays($currentYear)
    {
        // Прочтём SheduleRestDay для года, который указан в currentYear
        //    отсортируем по дате
        $restSheduleDays = SheduleRestDay::getYearRestDays($currentYear);

        $result = array();
        // Если не пустой вывод из базы
        if (count($restSheduleDays )!=0)
        {
            $keys = array_keys($restSheduleDays);
            $currentDate = $restSheduleDays[$keys[0]]['date'];
            // Перебираем результаты запроса и добавляем их в результат
            foreach ($restSheduleDays as $oneDay)
            {
                $oneDoctor = array();
                $oneDoctor['doctor'] = $oneDay['doctor_id'];
                $oneDoctor['type'] = $oneDay['type'];

                $result[ substr($oneDay['date'],0,10)][] = $oneDoctor;

            }

        }
        echo CJSON::encode(array('success' => true,
            'data' => $result));
    }

    public function actionSetHolidays() {
        if(!isset($_GET['dates'])) {
            echo CJSON::encode(array('success' => false,
                'msg' => 'Не могу сохранить выходной день!'));
            exit();
        }
        $dates = CJSON::decode($_GET['dates']);
        // Ничего устанавливать не надо
        if(count($dates) == 0) {
            echo CJSON::encode(array('success' => true,
                                     'data' => array()));
        }
        // Вычленяем год
        $parts = explode('-', $dates[0]);
        SheduleRestDay::model()->deleteAll('substr(cast(date AS text), 0, 5) = :year', array(':year' => $parts[0]));
        foreach($dates as $date) {
            $rest = new SheduleRestDay();
            $rest->date = $date;
            if(!$rest->save()) {
                echo CJSON::encode(array('success' => false,
                    'msg' => 'Не могу сохранить день в расписании!'));
                exit();
            }
        }
        echo CJSON::encode(array('success' => true,
            'data' => array()));
    }
}