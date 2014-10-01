<?php
class SheduleController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';

    public function actionView() {
        //var_dump("ля-ля");
        //exit();

        $ward = new Ward();
        $wardsResult = $ward->getRows(false, 'name', 'asc');
        $wardsList = array('-1' => 'Все отделения',
                            '-2'=> 'Без отделения');
        foreach($wardsResult as $key => $value) {
            $wardsList[$value['id']] = $value['name'];
        }

        // Вытащим врачей вместе с отделениями и со всем
        $doctorObject = new Doctor();
        $doctorsList = $doctorObject->getAllForSelect();
/*echo '<pre>';
        var_dump($doctorsList );
        exit();
*/

        // Делаем массив [доктор] => отделение
        $wardsForDoctor = array();
        foreach($doctorsList as $oneDoctor) {
            $wardsForDoctor[$oneDoctor['id']] = $oneDoctor['ward_code'];
        }

        array_unshift($doctorsList, array('id' => -1,'fio' => 'Все графики'));

        // Добавим врача "Все врачи"

        // Достанем кабинеты
        $cabinets = Cabinet::model()->getRows(false);

        // Создаём масситв кабинетов по их id-шникам
        $cabinetsByIds = array();

        foreach ($cabinets as $oneCabinet)
        {
            $cabinetsByIds[$oneCabinet['id']] = $oneCabinet['cab_number'];
        }

      //  var_dump($cabinets );
      //var_dump("!!");
      //  exit();

        $timetableFactObj = new TimetableFact();
        $factsSheduleForSelect = $timetableFactObj->getForSelect();
        $factsForJSON = array();
        foreach ($factsSheduleForSelect as $oneFact)
        {
            $factsForJSON [$oneFact['id']] = $oneFact['is_range'];
        }

        $this->render('index', array(
            'wardsList' => $wardsList,
            'doctorList' => $doctorsList,
            'doctorsForWards' => $wardsForDoctor,
            'cabinetsList' => $cabinets,
            'cabinetsListIds' => $cabinetsByIds,
            'factsForSelect' => $factsSheduleForSelect,
            'factsForJSON' => $factsForJSON
        ));
    }



    public function actionSave()
    {
        //var_dump($_GET);
        //var_dump($_POST);
        //exit();

        // Смотрим - если в базе есть расписание с заданным id - надо его найти. Иначе создать новое
        $timeTableModel = null;
        if ($_GET['timeTableId']!="")
        {
            //$timeTableObject = Timetable::model();
            $timeTableModel = Timetable::model()->findByPk($_GET['timeTableId']);
        }
        else
        {
            $timeTableModel = new Timetable();
          //  $timeTableModel->id=NULL;
        }

        // Проверяем дату начала и дату конца
        if ( (
            ($_GET['begin']=="") || (!isset($_GET['begin'])) || ($_GET['begin']==NULL)
            )
            ||
            (
                ($_GET['end']=="") || (!isset($_GET['end'])) || ($_GET['end']==NULL)
            )  ){
            echo CJSON::encode(array('success' => 'false',
                'errors' => array('Не заполнена дата начала или дата конца действия расписания' )));
            exit();
        }

        if ($_GET['timeTableId']!="")
        {
            // Удаляем все старые записи в таблице связей
            DoctorsTimetable::model()->deleteAll('id_timetable = :it', array(':it'=>$_GET['timeTableId']));

        }
        // Пишем новые значения в модель
        $timeTableModel->date_begin = $_GET['begin'];
        $timeTableModel->date_end = $_GET['end'];
        $timeTableModel->timetable_rules = $_GET['timeTableBody'];

        // Сохраняем расписание
        $timeTableModel->save();


        // Проверияем срок действия расписания
        $timetableId = null;
        if ($timeTableModel->EntryID!=null)
        {
            $timetableId = $timeTableModel->EntryID;
        }
        else
        {
            $timetableId = $_GET['timeTableId'];
        }

        $timeTableModel->id = $timetableId;
        $doctorsIds = CJSON::decode( $_GET['doctors'] );
        $this->checkTimetableTerms($timeTableModel, $doctorsIds[0]);

        // Перебираем докторов и пишем им запись в таблицу связей доктор<->расписание
        foreach ($doctorsIds as $oneDoctorIds)
        {
            // $oneDoctorIds - берём IDшник и записываем строку
            $doctorsTimeTableLink = new DoctorsTimetable();
            $doctorsTimeTableLink->id_doctor = $oneDoctorIds;
            /*if ($timeTableModel->EntryID!=null)
            {
                $doctorsTimeTableLink->id_timetable = $timeTableModel->EntryID;
            }
            else
            {
                $doctorsTimeTableLink->id_timetable = $_GET['timeTableId'];
            }
            */
            $doctorsTimeTableLink->id_timetable = $timetableId;
            $doctorsTimeTableLink->save();
        }

        /*
        echo CJSON::encode(array('success' => true,
            'msg' => 'Выходные дни успешно сохранены.'));
        */

        echo CJSON::encode(array('success' => true,
            'msg' => 'Расписание успешно сохранено.'));
    }

    public function actionGetShedule()
    {
        //var_dump($_GET);
        //var_dump($_POST);
        //exit();

        /*if (in_array('-1',$_GET['wards']))
        {
            echo("Нашёл Все отделения");
        }

        if (in_array('-2',$_GET['wards']))
        {
            echo("Нашёл Без отделения");
        }*/
        //var_dump($_GET);
        //exit();

        // Прокачиваем параметры списка
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

        // Тут возможно три случая:
        // 1. Все отделения или не указано вообще отделения, не указаны врачи
        //      в этом случае нет никаких ограничений - выбираем всех докторов, все расписания

        // 2. Указаны некоторые отделения, не указаны врачи (или все)
        //    в этом случае работает фильтр только по отделениям

        // 3. Указаны ID докторов. В этом случае плевать нам с высокой колокольни на отделения

        $doctors = null;
        $wards = null;
        $withoutWard = false;

        if ((isset($_GET['doctors'])) && (count($_GET['doctors'])>0))
        {
                if ($_GET['doctors'][0]!=-1)// если указаны "все врачи" - не фильтруем по врачам
                {
                    $doctors = $_GET['doctors'];
                }
        }
        else
        {
            // Если найдено "без отделений" - возводим флаг
            if (in_array('-2',$_GET['wards']))
            {
                $withoutWard = true;
            }
            else
            {
                // смотрим - если ли нет пункта "все отделения"
                if (!in_array('-1',$_GET['wards']))
                {
                    // Выбираем если массив отделений не пуст - присваиваем его в $wards
                    if (isset($_GET['wards']))
                    {
                        $wards = $_GET['wards'];
                    }
                }
            }
        }

       /* var_dump('Доктора=');
        var_dump($doctors);

        var_dump('Отделения=');
        var_dump($wards);

        var_dump($withoutWard);

        exit();
*/



        // Теперь, имеем данные для обращения к базе данных
        // Сконструируем специальный массив, хранящий данные для запроса
        $filterParameters = array(
            'doctorsIds' => $doctors,
            'wardsIds' => $wards,
            'woWardFlag' => $withoutWard

        );

        // =======> Test
        /*$filterParameters = array(
            'doctorsIds' => array('30','35'),
            'wardsIds' => null,
            'woWardFlag' => false

        );*/

        /*$filterParameters = array(
            'doctorsIds' => null,
            'wardsIds' => array('8'),
            'woWardFlag' => false

        );
        */

        /*$filterParameters = array(
            'doctorsIds' => null,
            'wardsIds' => null,
            'woWardFlag' => true

        );*/

        //<==========

        // Считаем предварительные данные для спискоты
        $timeTableObject = new Timetable();
        $num = $timeTableObject ->getNumRows($filterParameters);

        if(count($num) > 0) {
            $totalPages = ceil($num / $rows);
            $start = $page * $rows - $rows;
            $items = $timeTableObject->getRows($filterParameters , $sidx, $sord, $start, $rows);
        } else {
            $items = array();
            $totalPages = 0;
        }

        /*$doctors = null;
        // Смотрим - если не выбрано докторов - выбираем всех врачей по отделениям.
        if (isset($_GET['doctors']))
        {
            $doctors = $_GET['doctors'];
        }
        else
        {
            // Большая печалька(( потому что надо выбрать по отделениям всех докторов из этих отделений
            // Но если выбрано "все отделения", то это очень хорошо :) в этом случае не нужно ограничение на доктора
            //  А вот если
        }
        */
        echo CJSON::encode(
            array(
                'success'=> true,
                //'shedules'=> array( )
                 //'shedules'=> array( 1,2,3,4)



                'rows' => $items,
              //  'rows' => array( ),

                'total' => $totalPages,
                'records' => count($num)
            )
        );
    }

    function actionDeleteTimeTable()
    {
        $idTimeTable = $_GET['timetableId'];
        // Удаляем расписание
        Timetable::model()->deleteByPk($idTimeTable);
        // Удаляем связь между докторами т расписаниями, у которых проставлено это расписание
        DoctorsTimetable::model()->deleteAll('id_timetable = :timetable', array(':timetable'=>$_GET['timetableId']));

        echo CJSON::encode(array('success' => true,
            'msg' => 'Расписание успешно удалено!.'));
    }

    //======================>
/*
	public function actionGetWrittenPatientsEdit()
 	{
        $result = $this->getPatientsWrittenEdit();
        // В поле рекордс добавляем информацию о пациентах
        $this->getPatientsInfoToGreetings($result['rows']);
        $greetingsJSON = CJSON::encode($result);
        echo $greetingsJSON;
 	}
*/
    private function unwriteWritedPatients($dayBegin,$dayEnd,$doctorsIds)
    {
        try {
            //$doctorId= $_GET['doctor_id'];
            $doctors = array();

            // Инициализируем массив докторов


            if (isset($doctorsIds))
            {

                //var_dump($_GET['doctorsIds']);
                //exit();

                if (is_array($doctorsIds))
                {
                    $doctorsArr = $doctorsIds;
                }
                else
                {
                    $doctorsArr = CJSON::decode($doctorsIds);
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
                $doctors[] = $doctorsIds;
            }

            $model = new SheduleByDay();
            $greetings  = $model->getRangePatientsRows(false, $dayBegin, $dayEnd,$doctors);
            foreach($greetings as &$element) {
                // Берём и отписываем каждый приём по id-шнику
                SheduleByDay::model()->deleteByPk($element['id']);
                $this->writeCancelledGreeting($element);
            }
            return count($greetings) ;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    private function writeCancelledGreeting($greeting)
    {
        $newCancelledGreeting = new CancelledGreeting();

        $newCancelledGreeting->doctor_id = $greeting['doctor_id'];
        $newCancelledGreeting->medcard_id = $greeting['medcard_id'];
        if ($greeting['medcard_id']!='' && $greeting['medcard_id']!=null)
        {
            $newCancelledGreeting->policy_id = $greeting['oms_id'];
        }

        $newCancelledGreeting->patient_day = $greeting['patient_day'];
        $newCancelledGreeting->patient_time = $greeting['patient_time'];
        $newCancelledGreeting->mediate_id = $greeting['mediate_id'];
        $newCancelledGreeting->shedule_id = $greeting['shedule_id'];
        $newCancelledGreeting->greeting_type = $greeting['greeting_type'];
        $newCancelledGreeting->order_number = $greeting['order_number'];
        $newCancelledGreeting->comment = $greeting['comment'];
        $newCancelledGreeting->save();
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

            if ($oneGreeting['mediate_id']=='' || $oneGreeting['mediate_id']==null)
            {



                array_push( $directIds,$oneGreeting['id']);

               /* if ($oneGreeting['id']==2314)
                {
                    var_dump($directIds);
                    exit();
                }*/
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

    /*
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
            $oldGreetings =  SheduleByDay::model()->findAll('doctor_id = :doctor_id AND patient_day > :date_begin AND patient_day < :date_end AND patient_day>=current_date',
                array(':doctor_id' => $doctorId,':date_begin' => $oldShedule['date_begin'],
                    ':date_end' => $oldShedule['date_end']
                )
            );
            // Выбираем приёмы, которые попадают в промежуток теперь
            $newGreetings = SheduleByDay::model()->findAll('doctor_id = :doctor_id AND patient_day > :date_begin AND patient_day < :date_end AND patient_day>=current_date',
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
*/

    // Функция возвращает число пациентов, которые были отписаны при изменении расписания
    private function unwritePatientsOnTimetableChanged($doctorId, $dateBegin=false,$dateEnd=false, $timeBegin = false,$timeEnd = false)
    {
        $result = 0;

        // 1. Выбираем все приёмы у врача, которые старше сегодняшнего дня и текущего времени
        // 2. Проверяем их на то, вывалились ли они из расписания. Если приём вывалился - его надо запомнить в список
        // 3. Отписываем вывалившиеся приёмы и считаем их
        $findCondition = '( doctor_id = :doctor )';
        $findArray = array(':doctor'=>$doctorId);

        if ($dateBegin===false)
        {
            $findCondition .= '(time_begin is NULL) AND ( patient_day > '. date('Y-n-j') .')';
        }

        if ($timeBegin===false)
        {
            $findCondition .= 'AND ( (patient_time > ' .date('h:i:s'). ')) OR (patient_time is NULL)';
        }

        $sheduleByDayObject = new SheduleByDay();
        $greetingToCheck = $sheduleByDayObject::model()->findAll(
            $findCondition,
            $findArray
        );

        $maxGreetingDate = strtotime(date('Y-n-j'));
        $greetingDays = array();
        // Найдём максимальное число, на которое отменяется хотя бы один приём
        foreach ($greetingToCheck as $oneGreeting)
        {


            $greetingPatientDay = strtotime($oneGreeting['patient_day']);
            if (!in_array( $oneGreeting['patient_day'],$greetingDays ) )
            {
                array_push( $greetingDays ,  $oneGreeting['patient_day']);
            }
            if ($greetingPatientDay > $maxGreetingDate)
            {
                $maxGreetingDate = $greetingPatientDay;
            }

        }

        // Находим расписания для дат
        $shedules = $timeTable->getRows(
            array(
                'doctorsIds' => array($doctorId),
                'dateBegin' => date('Y-n-j'),
                'dateEnd' => date('Y-n-j',$maxGreetingDate)
            )
        );

        // Перебираем даты, на которой есть приёмы
        //   Для даты определяем, какому расписанию подчиняется эта дата
        //      а затем перебираем приёмы, записанные эту дату и проверяем каждый из приёмов.
        $greetingsIdToDelete = array();
        foreach ($greetingDays as $oneGreetingDate)
        {
            // Находим расписание для даты
            $sheduleForDay = null;
            foreach ($shedules as $oneShedule)
            {
                if ( (   strtotime($oneShedule['date_begin'])<=strtotime($oneGreetingDate)   ) &&
                    (strtotime($oneShedule['date_end'])>=strtotime($oneGreetingDate)  ) )
                {
                    $sheduleForDay  = $oneShedule ;
                    break;
                }
            }

            if ($sheduleForDay == null)
            {
                // Если для даты не нашли расписания - то приём на эту дату вывалились
                foreach ($greetingToCheck as $oneGreeting)
                {
                    if ( strtotime($oneGreeting['patient_date']) == strtotime($oneGreetingDate)  )
                    {
                        $greetingsIdToDelete[] = $oneGreeting['id'];
                    }
                }

            }
            else
            {
                // Тут надо проверить - попадают ли приёмы в расписание по времени (работает ли врач в то время, на которое записан пациент)
                // Получим правило из расписания
                $ruleToCheck = getRuleFromTimetable($sheduleForDay, date('Y-n-j',$oneGreetingDate));
                // Если правила нет - то добавляем все правила на этот день в удаление
                if ($ruleToCheck == null)
                {
                    foreach ($greetingToCheck as $oneGreeting)
                    {
                        if ( strtotime($oneGreeting['patient_date']) == strtotime($oneGreetingDate)  )
                        {
                            $greetingsIdToDelete[] = $oneGreeting['id'];
                        }
                    }
                }
                else
                {
                    // Вот тут сравниваем времена работы врача
                    foreach ($greetingToCheck as $oneGreeting)
                    {
                        if ( strtotime($oneGreeting['patient_date']) != strtotime($oneGreetingDate)  )
                        {
                            continue;
                        }
                        // Даты отсеяли - теперь проверяем на временной промежуток
                        if (!(
                        (  strtotime($oneGreeting['patient_time']) >=   strtotime($oneGreeting['greetingBegin']) )
                        &&
                        (   strtotime($oneGreeting['patient_time']) <   strtotime($oneGreeting['greetingEnd'])   )
                        ))
                        {
                            $greetingsIdToDelete[] = $oneGreeting['id'];
                        }
                        $greetingsIdToDelete[] = $oneGreeting['id'];
                    }
                }


            }

        }

        // отписываем приёмы, которые мы набрали
        if (  count($greetingsIdToDelete) > 0 )
        {
            $result = count($greetingsIdToDelete);

            // Сначала находим приёмы
            $idsStr = implode(',',$greetingsIdToDelete);
            $greetingsToDel = SheduleByDay::model()->findAll('id in ('. $idsStr .')');
            foreach ($greetingsToDel as $oneGreetingToDel)
            {
                $this->writeCancelledGreeting($oneGreetingToDel);
                SheduleByDay::model()->deleteByPk($oneGreetingToDel['id']);
            }
        }
        return $result;
    }

    /* Метод скорее всего не нужен будет*/
    private function unwriteWritedPatientsEdit($dayBegin,$dayEnd,$doctorId,$times,$sheduleId)
    {
        try {

            $idGreetingToCancel = array();
            //------------------
            // Дальше нужно собрать id тех приёмов, которые нужно отменить
            //------------------
            $oldShedule = SheduleSettedBe::model()->find('id = :id', array(':id'=>$sheduleId));

            // Выбираем приёмы, которые попадали раньше в промежуток
            $oldGreetings =  SheduleByDay::model()->findAll('doctor_id = :doctor_id AND patient_day >= :date_begin AND patient_day <= :date_end
                AND (patient_day>=current_date OR ((patient_time>=current_time)AND(patient_day=current_date)))',
                array(':doctor_id' => $doctorId,':date_begin' => $oldShedule['date_begin'],
                    ':date_end' => $oldShedule['date_end']
                )
            );
            // Выбираем приёмы, которые попадают в промежуток теперь
            $newGreetings = SheduleByDay::model()->findAll('doctor_id = :doctor_id AND patient_day >= :date_begin AND patient_day <= :date_end
                AND (patient_day>=current_date OR ((patient_time>=current_time)AND(patient_day=current_date)))',
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
                    if (!(strtotime($oneOldGreeting['patient_time'])>=strtotime($times['timesBegin'][$weekday]))
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
            $greetings = array();
            if ($idsString!='')
            {
                $greetings = $model->getGreetingsByIds(false, $idsString);
            }
            // Приведём дату в приличный вид и запишем ссылку для отписывания
            foreach($greetings as &$element) {
                // Берём и отписываем каждый приём по id-шнику
                SheduleByDay::model()->deleteByPk($element['id']);
                $this->writeCancelledGreeting($element);
            }
            return count ($greetings);
        } catch(Exception $e) {
            echo $e->getMessage();
        }

    }

    /*
    public function actionGetWrittenPatients()
 	{
        $result = $this->getPatientsWritten();
        // В поле рекордс добавляем информацию о пациентах
        $this->getPatientsInfoToGreetings($result['rows']);
        $greetingsJSON = CJSON::encode($result);
        echo $greetingsJSON;
 	}
    */
    /*

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
    */

    /*
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
    */

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
            $result = $this->addEditModelShedule($model);
            echo CJSON::encode(array('success' => 'true',
                                     'unwritedPatients' => $result,
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
        // Проверим - если мы редактируем, то вызываем одну функцию удаления предыдущих приёмов по старому расписанию
        //    иначе - другую
        $greetingsCount = 0;
        if ($model['sheduleEmployeeId']=='')
        {
            $greetingsCount = $this->unwriteWritedPatients($model['dateBegin'],$model['dateEnd'],$model['doctorId']);
        }
        else
        {
            // Приготовим времена
            $times = array();

            $times['timesBegin'] = array();
            $times['timesEnd'] = array();

            for ($i=0;$i<7;$i++)
            {
                $times['timesBegin'][$i] = $model['timeBegin'.$i];
                $times['timesEnd'][$i] = $model['timeEnd'.$i];
            }

            $greetingsCount = $this->unwriteWritedPatientsEdit($model['dateBegin'],$model['dateEnd'],
                        $model['doctorId'],$times,
                        $model['sheduleEmployeeId']);
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

        return $greetingsCount;
    }

	// Поидее нужно будет переписать
	public function actionDelete() {
        $result = 0;
			if (isset($_GET['id']))
			{
				 try {

                    // Тащим данные о смене
                    $sheduleToDelete = SheduleSettedBe::model()->findByPk($_GET['id']);
                    // Скармливаем функции отписывания пациентов
                     $result = $this->unwriteWritedPatients(
                         $sheduleToDelete ['date_begin'],
                         $sheduleToDelete ['date_end'],
                         $sheduleToDelete ['employee_id']
                     );

					// Сначала удаляем расписание по дням для дней недели
					SheduleSetted::model()->deleteAll('date_id = :date_id', array(
							':date_id' => $_GET['id']
							));
				
					// Удаляем смену
					SheduleSettedBe::model()->deleteAll('id = :id', array(
							':id' => $_GET['id']
							));
					echo CJSON::encode(array('success' => 'true',
                            'unwritedPatients' => $result,
							'text' => 'Расписание успешно удалено'));
				}
				catch (Exception $e)
				{
					
				}
					
			}
	}

    private function getSheduleObjectById($sheduleId)
    {
        $timeTableObject = new Timetable();
        return $timeTableObject::model()->findByPk($sheduleId);

    }

    private function checkTimetableTerms($timeTable, $doctorId)
    {
        $timeTableObject = new Timetable();
        // Выбираем графики по врачу
        $existingShedules = $timeTableObject->getRows(
            array(
                'doctorsIds' => array($doctorId)
            )
        );

        // Переберём расписания и подрежем у них сроки
        // Переберём смены
        foreach ($existingShedules as $oneShedule)
        {
            // Если смена не та, которую мы только что сохранили
            if ($oneShedule['id']!=$timeTable['id'])
            {
                if ((strtotime($oneShedule['date_begin'])>=strtotime($timeTable->date_begin))
                    &&(strtotime($oneShedule['date_end'])<=strtotime($timeTable->date_end)))
                {
                    // Новое расписание полностью перекрывает старое. Старое поидее надо удалить
                    $dateId =  $oneShedule['id'];
                    $sheduleToDelete = $this->getSheduleObjectById($dateId);

                    $sheduleToDelete ->delete();
                    // УБиваем строки из старой таблицы связей
                    DoctorsTimetable::model()->deleteAll('id_timetable = :timetable', array(
                        ':timetable' => $dateId
                    ));
                    continue;
                }

                if ((strtotime($oneShedule['date_begin'])<strtotime($timeTable->date_begin))
                    &&(strtotime($oneShedule['date_end'])>strtotime($timeTable->date_begin))
                    &&(strtotime($oneShedule['date_end'])<strtotime($timeTable->date_end)))
                {

                    // Новое расписание залезает на хвост старого. У старого надо изменить дату конца на дату, предшествующую
                    //   началу нового
                    $dateId = $oneShedule['id'];
                    $timeTableToChange = $this->getSheduleObjectById($dateId);

                    $timeTableToChange['date_end'] = date("Y-m-d",strtotime($timeTable->date_begin)-86400);
                    $timeTableToChange->save();
                    continue;
                }

                if ((strtotime($oneShedule['date_end'])>strtotime($timeTable->date_end))
                    &&(strtotime($oneShedule['date_begin'])>strtotime($timeTable->date_begin))
                    &&(strtotime($oneShedule['date_begin'])<strtotime($timeTable->date_end)))
                {
                    // Новое расписание залезает на голову старого. У старого надо изменить дату начала на дату после
                    //   конца нового
                    $dateId = $oneShedule['id'];
                    $timeTableToChange = $this->getSheduleObjectById($dateId);
                    $timeTableToChange['date_begin'] = date("Y-m-d",strtotime($timeTable->date_end)+86400);
                    $timeTableToChange->save();
                    continue;
                }

                if ((strtotime($oneShedule['date_begin'])<strtotime($timeTable->date_begin))
                    &&(strtotime($oneShedule['date_end'])>strtotime($timeTable->date_end)))
                {
                    // Новое раписание находится посередине старого. Старое расписание надо разбить на две части
                    // Алгоритм такой.
                    // 1. Сохраняем дату конца старого расписания
                    // 2. Дату конца старого расписания меняем как дата начала нового - 1 день
                    // 3. Вставляем новое расписание, которое ничем не будет отличаться
                    //   от старого кроме того, что у него дата начала будет равняться дате конца нового расписания + 1 день


                    // 1
                    $oldEndDate = $oneShedule['date_end'];
                    //var_dump($oneShedule);
                    //exit();
                    $oldContent = $oneShedule['json_data'];

                    $dateId = $oneShedule['id'];
                    $timeTableToChange = $this->getSheduleObjectById($dateId);

                    // 2
                    $timeTableToChange['date_end'] = date("Y-m-d",strtotime($timeTable->date_begin)-86400);
                    $timeTableToChange->save();

                    // 3
                    $addingShedule = new Timetable();
                    $addingShedule->date_begin = date("Y-m-d",strtotime($timeTable->date_end)+86400);
                    $addingShedule->date_end = 	$oldEndDate;
                    $addingShedule->timetable_rules = $oldContent;

                    $addingShedule->save();

                    $addingSheduleId = $addingShedule->EntryID;

                    // Нужно скопировать строки таблицы связи
                    $links = DoctorsTimetable::model()->findAll('id_timetable = :timetable', array( ':timetable' => $oneShedule['id']));

                    foreach ($links as $oneLink)
                    {
                        $newLink = new DoctorsTimetable();
                        $newLink->id_doctor = $oneLink['id_doctor'];
                        $newLink->id_timetable = $addingSheduleId;
                        $newLink->save();
                    }
                    continue;
                }

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
        $greetingsUnwrited = 0;
        $dateToDelete = array();
        // Получим данные для записи (они были заэнкожены, чтобы было проще передать)
        $dataToWrite = CJSON::decode($_POST['calendarData']);
        // С дат и докторов, которые мы хотим записать в базу необходимо
        //   Во-первых отписать приёмы, во вторых посчитать их
        // Перебираем даты, неа которые мы ставим выходные
        foreach ($dataToWrite as $key => $oneDate)
        {
            // Прочитываем даты, которые мы изменяем. Для этих дат мы должны удалить все строки в базе, чтобы потом их
            //    записать их по-новому
            $dateToDelete[] = $key;
            // С даты надо собрать ид докторов, у которых надо поотменять приёмы
            $doctorsForOneDay = array();
            foreach($oneDate as $oneDoctor)
            {
                array_push($doctorsForOneDay,$oneDoctor['doctor']);
            }
            // Для даты $oneDate и врачей $doctorsForOneDay надо отменить приёмы и
            //    прибавить число отменённых к $greetingsUnwrited
            // Если есть доктора на дату
            if (count($doctorsForOneDay)>0)
            {
                $greetingsUnwrited += ($this->unwriteWritedPatients($key,$key,$doctorsForOneDay));
            }
        }
        // Теперь надо убить все строки с датами, которые указаны в пришедших данных
        //var_dump($dataToWrite);
        //exit();
        // Удаляем
        SheduleRestDay::deleteDates($dateToDelete);
        // А теперь пишем обратно
        SheduleRestDay::writeAllRestDays($dataToWrite);
        echo CJSON::encode(array(
            'success' => true,
            'unwritedPatients' => $greetingsUnwrited,
            'data' => array()
        ));
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