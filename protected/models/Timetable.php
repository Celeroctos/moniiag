<?php
class Timetable extends MisActiveRecord {

    public $EntryID = null;

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function primaryKey()
    {
        return 'id';
        // Для составного первичного ключа следует использовать массив:
        // return array('pk1', 'pk2');
    }

    private function computeWeekNumber($dayToCount)
    {

        /*
        НеделяМесяца = ( День(ЗаданнаяДата) +
         НомерДняНедели( ПервыйДеньМесяца(ЗаданнаяДата) ) - 2 ) целочисленное_деление_на 7 + 1
        */

        return ((int) ((date("j", strtotime($dayToCount))+
                date("w", strtotime(
                    date("m", strtotime($dayToCount))
                    . "/01/" .
                    date("Y", strtotime($dayToCount))))-2)/7)) + 1;
    }

    public function getRuleFromTimetable($timeTable, $dayDate)
    {
        $currentDateToCompare = strtotime($dayDate);

        // Берём и раскодируем правило в об'ект
        $timeTableObject = CJSON::decode($timeTable['json_data']);

        $weekDayOfDayDate = date("w", strtotime($dayDate));
        $dayFromDate = date("j", strtotime($dayDate));
        if ($weekDayOfDayDate == 0) {$weekDayOfDayDate = 7;}
        // Считаем номер недели длля дня
        $weekNumberOfDayDate = $this->computeWeekNumber($dayDate);
        $underFact = false;

        $ruleToApply = null;

        // =====>
        // 1. Перебираем правила и смотрим совпадение с датой
        foreach ($timeTableObject['rules'] as $oneRule) {
            if (isset($oneRule['daysDates'])) {
                foreach($oneRule['daysDates'] as $oneDate) {
                    $oneDateFromTimetable = strtotime($oneDate);
                    if ($oneDateFromTimetable == $currentDateToCompare) {
                        // Дата попадает в день, указанный в расписании
                        $ruleToApply = $oneRule;
                    }
                }
            }
        }
        // Если правило не нуль - то не проверяем дальше
        if ($ruleToApply!=null){
            return $ruleToApply;
        }
		
        // Перебираем обстоятельства
        $underFact = array();
		foreach($timeTableObject['facts'] as $oneFact) {
            $dateBeginFact = strtotime($oneFact['begin']);
            // Если промежуток - проверяем, попадает ли день в этот промежуток. Иначе проверяем на равенство даты
            if($oneFact['isRange']==1) {
                $dateEndFact = strtotime($oneFact['end']);
                if (($currentDateToCompare >=$dateBeginFact)&&($currentDateToCompare <=$dateEndFact )) {
                    $underFact = $oneFact;
                    break;
                }
            } else{
                if($currentDateToCompare ==$dateBeginFact) {
                    $underFact = $oneFact;
                    break;
                }
            }
        }

        // Если обстоятельство нашли
        if($underFact) {
			return array('isFact' => 1) + $underFact;
           // return null;
        }

        foreach ($timeTableObject['rules'] as $oneRule) {
            // Переменные "есть дни" и "есть чётность", "совпадение по чётности", "совпадение по дням"
            // Механизм следующий: перебираем правила, смотрим - если не указана ни чётность ни дни - то не применяется правило
            //     в противном случае - флаг совпадения по критерию должен быть равен флагу наличия данного критерия
            $dayWeekCoidance = false;
            $oddanceCoidance = false;
            $issetDays = false;
            $issetOddance = false;
            $ruleToApply = $oneRule;
            if (isset($oneRule['days']) && (count($oneRule['days']) > 0)) {
                $issetDays = true;
                if (isset($oneRule['days'][$weekDayOfDayDate ])){
                    // Проверяем - подходит ли день под правило. Если нет - то вызываем continue
                    //    если да - то даём проверить его на чётность (нечётность). Если и чётность/нечетность он не проходит
                    //     -то вызываем continue уже в конце
                    if (count($oneRule['days'][$weekDayOfDayDate ]) > 0) {
                        // Ищем в массиве значение, равное номеру недели
                        // если не нашли - сразу выходим
                       // $wasDayFound = false;
                        for($i = 0; $i < count($oneRule['days'][$weekDayOfDayDate ]); $i++) {
                            if ($oneRule['days'][$weekDayOfDayDate ][$i]==$weekNumberOfDayDate) {
                               // $wasDayFound = true;
                                $dayWeekCoidance = true;
                            }
                        }
                        /*if (!$wasDayFound)
                        {
                            $ruleToApply = null;
                        }*/
                    } else {
                       // $wasDayFound = true;
                        $dayWeekCoidance = true;
                    }
                }
            }

            // 3. Смотрим - попадает ли дата в чётные/нечётные
            //     Если указана чётность, то надо проверить - подпадает ли день под чётный/нечетный
            if (isset($oneRule['oddance']))
            {
                $oddanceCoidance = true;
                $issetOddance = true;
                if ($oneRule['oddance']==1)
                {
                    // Если день нечётный - то выходим
                    if ($dayFromDate % 2 == 1)
                    {
                        $oddanceCoidance = false;
                    }


                }
                if ($oneRule['oddance']==0)
                {
                    // Если день чётный - то выходим
                    if ($dayFromDate % 2 == 0)
                    {
                        $oddanceCoidance = false;
                    }
                }

                // Проверим - если указано поле "кроме" и день попадает в значение этого поля
                //   - то смотрим следующее правило
                if (isset($oneRule['except']))
                {
                    if ( in_array($weekDayOfDayDate,$oneRule['except']) )
                    {
                        // Досвидос - нельзя применять данное правило
                        $oddanceCoidance = false;
                    }
                }

            }

            // Если мы не нашли правило - идём на начало
            if (
                ($dayWeekCoidance!=$issetDays )
                ||
                ($oddanceCoidance!=$issetOddance)
                || (($issetDays==false) &&($issetOddance==false) )
            )
            {
                continue;
            }
            else
            {
                return $ruleToApply;
            }
        }


        return null;
    }

    public function afterSave() {
        try{
            // Надо будет разобраться почему без вот этой вот конструкции не возвращается назад ID-шник только что добавленной записи
            $this->EntryID = Yii::app()->db->getLastInsertID('mis.timetable_id_seq');
        }
        catch(Exception $Exc)
        {

        }
        return parent::afterSave();
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false)
    {
        $timeTablesIds = $this->getRowsIds($filters, $sidx, $sord , $start , $limit );

        // А теперь для каждого расписания (столько расписаний, сколько на одной странице в спискоте, т.е. штук 15) выбираем
        //   - date_begin
        //   - date_end
        //   - json
        //   - id (само собой)
        //   -doctors
        //    в массиве doctors имеем распределение докторов по отделениям

        $timeTableObject = new Timetable();
        $timeTableDoctorsObject = new DoctorsTimetable();

        foreach ($timeTablesIds as &$oneTableId)
        {
            // Берём данные по самому расписанию
            $timetableBody = $timeTableObject::model()->findByPk( $oneTableId['id'] );
            //$oneTableId['id'] = $timetableBody['id'];
            $oneTableId['date_begin'] = $timetableBody['date_begin'];
            $oneTableId['date_end'] = $timetableBody['date_end'];
            $oneTableId['json_data'] = $timetableBody['timetable_rules'];
            $oneTableId['wardsWithDoctors'] = $timeTableDoctorsObject->getDoctorsWardsByShedule($oneTableId['id']);


        }

        return $timeTablesIds;
    }


    public function getNumRows($filters, $sidx = false, $sord = false, $start = false, $limit = false)
    {
        // Получим строки с ID и посчитаем их
        return count(  $this->getRowsIds($filters, $sidx, $sord , $start , $limit ) );
    }

    private function getRowsIds($filters, $sidx = false, $sord = false, $start = false, $limit = false)
    {
        try
        {
            $connection = $this->getDbConnection();
            $timetables = $connection->createCommand()
                ->selectDistinct('tt.id, tt.date_end')
                ->from(Timetable::model()->tableName().' tt')
                ->join(DoctorsTimetable::model()->tableName(). ' dtt', 'dtt.id_timetable = tt.id')
                ->join(Doctor::model()->tableName(). ' d', 'd.id =dtt.id_doctor')
                ->leftJoin(Ward::model()->tableName(). ' w', 'w.id = d.ward_code');

                // Смотрим - если заданы врачи - пришпиливаем к запросу врачей
                if ($filters['doctorsIds']!=NULL)
                {
                    $timetables->andWhere('d.id in ('. implode(',',$filters['doctorsIds']) .')');
                }
                else
                {
                    // Иначе - проверяем флаг "без отделения"
                    if ($filters['woWardFlag']==true)
                    {
                        $timetables->andWhere('d.ward_code is NULL');
                    }
                    else
                    {
                       if ($filters['wardsIds']!=NULL)
                       {
                           // Иначе проверяем заданные отделения
                           $timetables->andWhere('d.ward_code in ('. implode(',',$filters['wardsIds']) .')');
                       }
                    }
                }

            // Если заданы даты начала и даты конца действия расписания - то учитываем эти условия
            if ( (isset($filters['dateBegin']))&&(isset($filters['dateEnd'])) )
            {
                $timetables->andWhere('(  (  ( tt.date_begin <= :begin  )AND(  tt.date_end >= :end ) )
                                        OR(  ( tt.date_begin <= :begin  )AND(  tt.date_end >= :begin )  )
                                        OR(  ( tt.date_begin <= :end  )AND(  tt.date_end >= :end ))
                                        OR(  ( tt.date_begin >= :begin  )AND(  tt.date_end <= :end ))
                                       )',
                                     array(
                                         ':begin' => $filters['dateBegin'],
                                         ':end' => $filters['dateEnd']
                                     )
                );
            }

            if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
                $timetables->order($sidx. ', tt.date_end '.$sord);
                $timetables->limit($limit, $start);
            }


//var_dump($timetables);
  //          exit();

            $timetables = $timetables->queryAll();

           // var_dump($timetables );
           // exit();
            return $timetables;
        }
        catch (Exception $Exc)
        {
            var_dump($Exc);
            exit();
        }
    }

    public function tableName()
    {
        return 'mis.timetable';
    }

}

?>