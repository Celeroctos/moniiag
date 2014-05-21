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
 			->select('dsbd.*, 
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
 		$doctorId, 
 		$sidx = false, 
 		$sord = false, 
 		$start = false, 
 		$limit = false
 	) {
 	
 		$connection = Yii::app()->db;
 		$patients = $connection->createCommand()
 			->select('dsbd.*, 
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
 			->where('dsbd.doctor_id = :doctor_id
 					AND dsbd.patient_day >= :beginDate
 					AND dsbd.patient_day <= :endDate
 					', array(
 						':beginDate' => $dateBegin,
 						':endDate' => $dateEnd, 
 						':doctor_id' => $doctorId
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
	
    public function getRows($date, $doctorId, $withMediate = 1, $onlyForDoctor = 0) {
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
        $patients->order('dsbd.patient_time');
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

    // Получить список приёмов по критериям
    public function getGreetingsPerQrit($filters, $start = false, $limit = false) {
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
                    )
                ), array(
                    'o' => array('p_first_name', 'p_middle_name', 'p_last_name', 'patient_fio', 'patient_ids'),
                    'd' => array('d_first_name', 'd_middle_name', 'd_last_name', 'doctor_fio', 'doctor_ids'),
                    'm' => array('phone'),
                    'mdp' => array('m_first_name', 'm_middle_name', 'm_last_name', 'patient_fio', 'phone'),
                    'dsbd' => array('patient_day', 'medcard_id')
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
                    'patient_ids' => 'id',
                    'doctor_ids' => 'id'
                ));
            }

            $greetings->order('dsbd.patient_time');
            $greetings->group('dsbd.id, o.first_name, o.last_name, o.middle_name, d.first_name, d.last_name, d.middle_name, m.motion, o.id, mp.name, m.card_number, mdp.phone, mdp.last_name, mdp.middle_name, mdp.first_name');

            if($limit !== false && $start !== false) {
                $greetings->limit($limit, $start);
            }

            return $greetings->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>