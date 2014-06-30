<?php
class SheduleByDay extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.doctor_shedule_by_day';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $cabinet = $connection->createCommand()
                ->select('c.*')
                ->from('mis.cabinets c')
                ->where('c.id = :id', array(':id' => $id))
                ->queryRow();

            return $cabinet;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    /*
	public function getGreetingsByIds(
 		$filters,
 		$idsString, 
 		$sidx = false, 
 		$sord = false, 
 		$start = false, 
 		$limit = false
 		)
 	{
 		
 		$connection = Yii::app()->db;
 		$patients = $connection->createCommand()
 			->select('dsbd.*, d.first_name,d.middle_name,d.last_name,
 					CONCAT(o.last_name, \' \', o.first_name, \' \', o.middle_name ) as fio, 
 					m.card_number AS card_number,
 					SUBSTR(
 						CAST(dsbd.patient_time AS text),
 						0, 
 						CHAR_LENGTH(CAST(dsbd.patient_time AS text)) - 2
 					) AS patient_time,
 					m.contact')
 			->from('mis.doctor_shedule_by_day dsbd')
 			->Join('mis.medcards m', 'dsbd.medcard_id = m.card_number')
 			->Join('mis.oms o', 'm.policy_id = o.id')
            ->leftJoin('mis.doctors d', 'dsbd.doctor_id = d.id')
 			->where('dsbd.id in ('.$idsString.')
 					'
 				);
 		
 		
 		
 		if($sidx !== false && $sord !== false)
 		{
 			$patients->order($sidx.' '.$sord);
 		}
 		
 		if ($start !== false && $limit !== false)
 		{	
 			$patients->limit($limit, $start);
 		}
 		
 		return $patients->queryAll();
 	}
    */

    public function getGreetingsByIds(
        $filters,
        $idsString,
        $sidx = false,
        $sord = false,
        $start = false,
        $limit = false
    )
    {

        $connection = Yii::app()->db;
        $patients = $connection->createCommand()
            ->select('dsbd.*, o.id as oms_id, d.first_name,d.middle_name,d.last_name,
 					SUBSTR(
 						CAST(dsbd.patient_time AS text),
 						0,
 						CHAR_LENGTH(CAST(dsbd.patient_time AS text)) - 2
 					) AS patient_time')
            ->from('mis.doctor_shedule_by_day dsbd')
            ->leftJoin('mis.medcards m', 'dsbd.medcard_id = m.card_number')
            ->leftJoin('mis.oms o', 'm.policy_id = o.id')
            ->leftJoin('mis.doctors d', 'dsbd.doctor_id = d.id')
            ->where('dsbd.id in ('.$idsString.')
 					'
            );



        if($sidx !== false && $sord !== false)
        {
            $patients->order($sidx.' '.$sord);
        }

        if ($start !== false && $limit !== false)
        {
            $patients->limit($limit, $start);
        }

        return $patients->queryAll();
    }

 	public function getRangePatientsRows(
		$filters,
 		$dateBegin,
 		$dateEnd, 
 		$doctorIds,
 		$sidx = false, 
 		$sord = false, 
 		$start = false, 
 		$limit = false
 	) {

        $doctorStr = implode(",",$doctorIds);
 		$connection = Yii::app()->db;
 		$patients = $connection->createCommand()
        /*
 		+ 		CONCAT(o.last_name, \' \', o.first_name, \' \', o.middle_name ) as fio,
 					m.card_number AS card_number
                    m.contact
        */
 			->select('dsbd.*, o.id as oms_id, d.first_name,d.middle_name,d.last_name,
 					SUBSTR(
 						CAST(dsbd.patient_time AS text),
 						0, 
 						CHAR_LENGTH(CAST(dsbd.patient_time AS text)) - 2
 					) AS patient_time')
 			->from('mis.doctor_shedule_by_day dsbd')
 			->leftJoin('mis.medcards m', 'dsbd.medcard_id = m.card_number')
 			->leftJoin('mis.oms o', 'm.policy_id = o.id')
            ->leftJoin('mis.doctors d', 'dsbd.doctor_id = d.id')
 			->where('dsbd.doctor_id in ('.$doctorStr.')
 					AND dsbd.patient_day >= :beginDate
 					AND dsbd.patient_day <= :endDate
 					AND dsbd.patient_day >= current_date
 					', array(
 						':beginDate' => $dateBegin,
 						':endDate' => $dateEnd
 					)
 			);
 		
		if($filters !== false) {
            $this->getSearchConditions($patients, $filters, array(
            ), array(
                'o' => array('phone', 'id'),
                'm' => array('card_number'),
				'dsbd' => array('patient_day', 'doctor_id')
            ), array(

            ));
        }
 	
 	
 		if($sidx !== false && $sord !== false)
 		{
 			$patients->order($sidx.' '.$sord);
 		}
 		
 		if ($start !== false && $limit !== false)
 		{	
 			$patients->limit($limit, $start);
 		}

 		return $patients->queryAll();
 	}

    public function getRows($date, $doctorId, $withMediate = 1, $onlyForDoctor = 0, $onlyWaitingLine = 0) {

        $connection = Yii::app()->db;
        // Здесь есть анальный баг с повтором строк..
        $patients = $connection->createCommand()
            ->selectDistinct('dsbd.*,
                            CONCAT(o.last_name, \' \', o.first_name, \' \', o.middle_name ) as fio,
                            m.card_number AS card_number,
                            SUBSTR(CAST(dsbd.patient_time AS text),
                            0, CHAR_LENGTH(CAST(dsbd.patient_time AS text)) - 2) AS patient_time,
                            m.motion')
            ->from('mis.doctor_shedule_by_day dsbd')
            ->leftJoin('mis.medcards m', 'dsbd.medcard_id = m.card_number')
            ->leftJoin('mis.oms o', 'm.policy_id = o.id')
            ->leftJoin('mis.users u', 'u.employee_id = dsbd.doctor_id');
        if($withMediate) {
            $patients->leftJoin('mis.mediate_patients mdp', 'mdp.id = dsbd.mediate_id');
        }

        $patients->where('dsbd.doctor_id = :doctor_id AND dsbd.patient_day = :patient_day', array(':patient_day' => $date, ':doctor_id' => $doctorId));
        if(!$withMediate) {
            $patients->andWhere('dsbd.mediate_id IS NULL');
        }
        if($onlyForDoctor) {
            $patients->andWhere('m.motion = 1 OR (m.motion = 0 AND dsbd.is_accepted = 1)');
        }

        if($onlyWaitingLine) {
            $patients->andWhere('dsbd.order_number IS NOT NULL');
            $patients->order('dsbd.order_number');
        } else {
            $patients->andWhere('dsbd.order_number IS NULL');
            $patients->order('dsbd.patient_time');
        }

        return $patients->queryAll();
    }

    // Получить информацию о пациентах на опосредованные приёмы
    public static function getMediateGreetingsInfo($greetingIds)
    {
        $greetingsIdsStr = implode(",",$greetingIds);
        $connection = Yii::app()->db;
        $patients = $connection->createCommand()
            ->select('dsbd.id,
                CONCAT(mp.last_name, \' \', mp.first_name, \' \', mp.middle_name ) as fio,
                mp.phone as contact
            ')
            ->from('mis.doctor_shedule_by_day dsbd')
           ->Join('mis.mediate_patients mp', 'dsbd.mediate_id = mp.id')
            ->where('dsbd.id in ('. $greetingsIdsStr.')',
                array(
                )
            );
        return $patients->queryAll();
    }

    // Получить информацию о пациентах на обычные приёмы
    public static function getDirectGreetingsInfo($greetingIds)
    {
        $greetingsIdsStr = implode(",",$greetingIds);
        $connection = Yii::app()->db;
        $patients = $connection->createCommand()
            /*
             + 		CONCAT(o.last_name, \' \', o.first_name, \' \', o.middle_name ) as fio,
                         m.card_number AS card_number
                        m.contact
            */
            ->select('dsbd.id,
 					CONCAT(o.last_name, \' \', o.first_name, \' \', o.middle_name ) as fio,
                    m.contact,
                    m.card_number AS card_number
 					')
            ->from('mis.doctor_shedule_by_day dsbd')
            ->leftJoin('mis.medcards m', 'dsbd.medcard_id = m.card_number')
            ->leftJoin('mis.oms o', 'm.policy_id = o.id')
            ->where('dsbd.id in ('.$greetingsIdsStr.')', array()
            );
        return $patients->queryAll();

    }

    public function getByEnterprise($id) {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getDaysWithPatients($userId) {
        $connection = Yii::app()->db;
        $dates = $connection->createCommand()
            ->selectDistinct('dsbd.patient_day')
            ->from('mis.doctor_shedule_by_day dsbd')
            ->leftJoin('mis.users u', 'dsbd.doctor_id = u.employee_id')
            ->where('u.id = :id', array(':id' => $userId));
        return $dates->queryAll();
    }

    public static function sortSheduleElements($sheduleElements) {
        usort($sheduleElements, function($element1, $element2) {
            if($element1['doctor_id'] == $element2['doctor_id']) {
                return 0;
            } elseif($element1['doctor_id'] < $element2['doctor_id']) {
                return -1;
            } else {
                return 1;
            }
        });
        return $sheduleElements;
    }


    public static function makeClusters($sheduleElements) {
        $num = count($sheduleElements);
        // Делим на кластеры. Каждый кластер сортируем по времени (по дефолту) или по заданному полю
        // Сортируем по времени
        if(count($sheduleElements) > 1) {
            $sheduleElementsSorted = array();
            $currentDoctorId = $sheduleElements[0]['doctor_id'];
            $cluster = array($sheduleElements[0]);
            for($i = 1; $i < $num; $i++) {
                if($sheduleElements[$i]['doctor_id'] == $currentDoctorId && $i < $num - 1) {
                    $cluster[] = $sheduleElements[$i];
                } else {
                    if($i == $num - 1) {
                        array_push($cluster, $sheduleElements[$i]);
                    }
                    usort($cluster, function($element1, $element2) {
                        $time1 = strtotime($_GET['date'].' '.$element1['patient_time']);
                        $time2 = strtotime($_GET['date'].' '.$element2['patient_time']);
                        if($time1 < $time2) {
                            return -1;
                        } elseif($time1 > $time2) {
                            return 1;
                        } else {
                            return 0;
                        }
                    });

                    foreach($cluster as $element) {
                        array_push($sheduleElementsSorted, $element);
                    }

                    $cluster = array($sheduleElements[$i]);
                    $currentDoctorId = $sheduleElements[$i]['doctor_id'];
                }
            }
            $sheduleElements = $sheduleElementsSorted;
        }
        return $sheduleElements;
    }

    // Получить список приёмов по критериям
    public function getGreetingsPerQrit($filters, $start = false, $limit = false, $mediateOnly = false) {
        try {
            $connection = Yii::app()->db;
            $greetings = $connection->createCommand()
                ->selectDistinct('dsbd.*,
                                  o.first_name as p_first_name,
                                  o.middle_name as p_middle_name,
                                  o.last_name as p_last_name,
                                  d.first_name as d_first_name,
                                  d.middle_name as d_middle_name,
                                  d.last_name as d_last_name,
                                  m.motion,
                                  m.card_number,
                                  m.contact,
                                  mdp.phone,
                                  mdp.first_name as m_first_name,
                                  mdp.middle_name as m_middle_name,
                                  mdp.last_name as m_last_name,
                                  o.id as oms_id,
                                  mp.name as post')
                ->from('mis.doctor_shedule_by_day dsbd')
                ->leftJoin('mis.medcards m', 'dsbd.medcard_id = m.card_number')
                ->leftJoin('mis.oms o', 'm.policy_id = o.id')
                ->join('mis.doctors d', 'd.id = dsbd.doctor_id')
                ->join('mis.medpersonal mp', 'd.post_id = mp.id')
                ->leftJoin('mis.mediate_patients mdp', 'mdp.id = dsbd.mediate_id');


            if($filters !== false) {
                $this->getSearchConditions($greetings, $filters, array(
                    'doctor_fio' => array(
                        'd_first_name',
                        'd_last_name',
                        'd_middle_name'
                    ),
                    'patient_fio' => array(
                        'p_first_name',
                        'p_last_name',
                        'p_middle_name',
                        'm_last_name',
                        'm_first_name',
                        'm_middle_name'
                    ),
                    'phone' => array(
                        'contact',
                        'm_phone'
                    )
                ), array(
                    'mp' => array('is_for_pregnants'),
                    'o' => array('p_first_name', 'p_middle_name', 'p_last_name', 'patient_fio', 'patients_ids'),
                    'd' => array('d_first_name', 'd_middle_name', 'd_last_name', 'doctor_fio', 'doctors_ids'),
                    'm' => array('contact'),
                    'mdp' => array('m_first_name', 'm_middle_name', 'm_last_name', 'patient_fio', 'm_phone'),
                    'dsbd' => array('patient_day', 'medcard_id', 'mediates_ids')
                ), array(
                    'phone' => 'contact',
                    'd_first_name' => 'first_name',
                    'd_last_name' => 'last_name',
                    'd_middle_name' => 'middle_name',
                    'p_first_name' => 'first_name',
                    'p_last_name' => 'last_name',
                    'p_middle_name' => 'middle_name',
                    'm_last_name' => 'last_name',
                    'm_first_name' => 'first_name',
                    'm_middle_name' => 'middle_name',
                    'patients_ids' => 'id',
                    'doctors_ids' => 'id',
                    'mediates_ids' => 'mediate_id',
                    'm_phone' => 'phone'
                ), array(
                    'OR' => array(
                        'mediates_ids',
                        'pateints_ids'
                    )
                ));
            }
            if($mediateOnly) {
                $greetings->andWhere('dsbd.mediate_id IS NOT NULL');
            }

            $greetings->order('dsbd.patient_time');
            $greetings->group('dsbd.id, o.first_name, o.last_name, o.middle_name, d.first_name, d.last_name, d.middle_name, m.motion, o.id, mp.name, m.card_number, mdp.phone, mdp.last_name, mdp.middle_name, mdp.first_name');

            if($limit !== false && $start !== false) {
                $greetings->limit($limit, $start);
            }

           //var_dump($greetings->text);
            //exit();
            $result = $greetings->queryAll();
            return $result;
          //  var_dump($result );
          //  exit();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>