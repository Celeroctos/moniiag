<?php
class Doctor extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.doctors';
    }


    public function getAll($forPregnant = false) {
        try {
            $connection = Yii::app()->db;
            $doctors = $connection->createCommand()
                ->select('d.*')
                ->from('mis.doctors d');

            if($forPregnant !== false) {
                $doctors->join('mis.medpersonal m', 'd.post_id = m.id')
                    ->where('m.is_for_pregnants = 1');
            }
            $doctors = $doctors->queryAll();

            foreach($doctors as $key => &$doctor) {
                $doctor['fio'] = $doctor['first_name'].' '.$doctor['middle_name'].' '.$doctor['last_name'];
            }

            return $doctors;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getAllForSelect($forPregnant = false) {
        try {
            $connection = Yii::app()->db;
            $doctors = $connection->createCommand()
                ->select('d.*')
                ->from('mis.doctors d');

            if($forPregnant !== false) {
                $doctors->join('mis.medpersonal m', 'd.post_id = m.id')
                    ->where('m.is_for_pregnants = 1');
            }
            $doctors->order('d.last_name asc');
            $doctors = $doctors->queryAll();

            foreach($doctors as $key => &$doctor) {
                $doctor['fio'] = $doctor['last_name'].' '.$doctor['first_name'].' '.$doctor['middle_name'];
            }

            return $doctors;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false,
                    $limit = false, $choosedDiagnosis = array(), $greetingDate = false, $calendarType = 0, $isCallCenter = false) {

        $connection = Yii::app()->db;
        $doctor = $connection->createCommand()
            ->selectDistinct('d.*, w.name as ward, m.name as post')
            ->from('mis.doctors d')
            ->leftJoin('mis.wards w', 'd.ward_code = w.id')
            ->leftJoin('mis.medpersonal m', 'd.post_id = m.id');

           if(count($choosedDiagnosis) > 0) {
               $doctor->leftJoin('mis.mkb10_distrib md', 'md.employee_id = d.id');
           }

         if($filters !== false) {
              $this->getSearchConditions($doctor, $filters, array(
              ), array(
                  'd' => array('id', 'first_name', 'last_name', 'middle_name', 'post_id', 'ward_code', 'greeting_type'),
                  'm' => array('is_for_pregnants')
              ), array(

              ));
          }

         if(count($choosedDiagnosis) > 0) {
              $doctor->andWhere(array('in', 'md.mkb10_id', $choosedDiagnosis));
         }

         if($isCallCenter) {
             $doctor->andWhere('d.display_in_callcenter = 1');
         }
         $doctor->andWhere('m.is_medworker = 1');

          // Теперь нужно выяснить сотрудников, которые могут принимать в этот день
          if($greetingDate !== false && $greetingDate !== null) {
              // Теперь мы знаем, каких врачей выбирать, с каким днём
              if($calendarType == 0) {
                $doctorsPerDay = SheduleSetted::model()->getAllPerDate($greetingDate);
			  } else { // Это выбирает врачей в промежутке
                $doctorsPerDay = SheduleSetted::model()->getAllPerDates($greetingDate);
              }
              $doctorIds = array();
              $num = count($doctorsPerDay);
              for($i = 0; $i < $num; $i++) {
                  $doctorIds[] = $doctorsPerDay[$i]['employee_id'];
              }
              $doctor->andWhere(array('in', 'd.id', $doctorIds));
          }

          if ($sidx && $sord) {
			$doctor->order($sidx.' '.$sord);
          }

		  if($limit && $start) {
			$doctor->limit($limit, $start);
		  }

        $doctors = $doctor->queryAll();
        return $doctors;
    }
	
	public function getDoctorStat($filters) {
		$connection = Yii::app()->db;
        $doctor = $connection->createCommand()
            ->select('d.*, w.name as ward, w.id as ward_id, m.name as post, m.id as post_id, dsbd.greeting_type, dsbd.patient_day, dsbd.order_number, mc.reg_date')
            ->from('mis.doctors d')
            ->leftJoin('mis.wards w', 'd.ward_code = w.id')
            ->leftJoin('mis.medpersonal m', 'd.post_id = m.id')
			->leftJoin(SheduleByDay::model()->tableName().' dsbd', 'dsbd.doctor_id = d.id')
			->leftJoin('mis.medcards mc', 'dsbd.medcard_id = mc.card_number');
		
		if($filters !== false) {
			$this->getSearchConditions($doctor, $filters, array(
			), array(
				  'd' => array('doctor_id', 'id'),
				  'm' => array('medworker_id', 'id'),
				  'w' => array('ward_id', 'id'),
				  'dsbd' => array('patient_day', 'patient_day_to', 'patient_day_from')
			), array(
				'doctor_id' => 'id',
				'medworker_id' => 'id',
				'ward_id' => 'id',
				'patient_day_to' => 'patient_day',
				'patient_day_from' => 'patient_day'
			));
		 }
		$doctor->andWhere('dsbd.time_begin IS NOT NULL');
		$doctor->andWhere('m.is_medworker = 1');
		
		$doctors = $doctor->queryAll();
		$resultArr = array();
		foreach($doctors as $doctor) {
			// Несуществующее отделение
			if(!isset($resultArr[(string)$doctor['ward_id']])) {
				$resultArr[(string)$doctor['ward_id']] = array(
					'name' => $doctor['ward'] != null ? $doctor['ward'] : 'Неизвестное отделение',
					'numAllGreetings' => 0,
					'primaryPerWriting' => 0,
					'primaryPerQueue' => 0,
					'secondaryPerWriting' => 0,
					'secondaryPerQueue' => 0,
					'elements' => array()
				);
			}
			// Несуществующая специальность
			if(!isset($resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']])) {
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']] = array(
					'name' => $doctor['post'] != null ? $doctor['post'] : 'Неизвестная специальность',
					'numAllGreetings' => 0,
					'primaryPerWriting' => 0,
					'primaryPerQueue' => 0,
					'secondaryPerWriting' => 0,
					'secondaryPerQueue' => 0,
					'elements' => array()
				);
			}
			
			// Несуществующий врач
			if(!isset($resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']])) {
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']] = array(
					'name' => $doctor['last_name'].' '.$doctor['first_name'].' '.$doctor['middle_name'],
					'data' => array(
						'numAllGreetings' => 0,
						'primaryPerWriting' => 0,
						'primaryPerQueue' => 0,
						'secondaryPerWriting' => 0,
						'secondaryPerQueue' => 0
					)
				);
			}
			
			// 2.	Первичные приемы – прием в тот же день, в который завели или перерегистрировали карту (дали новый номер)
			if($doctor['patient_day'] == $doctor['reg_date']) { // Первичный
				if($doctor['order_number'] == null) {
					$resultArr[(string)$doctor['ward_id']]['primaryPerWriting']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['primaryPerWriting']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['primaryPerWriting']++;
				} else {
					$resultArr[(string)$doctor['ward_id']]['primaryPerQueue']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['primaryPerQueue']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['primaryPerQueue']++;
				}
			// 3.	Вторичные приемы – приемы в дни, отличающиеся от дня заведения/перерегистрации карты. 
			} else { // Вторичный
				if($doctor['order_number'] == null) {
					$resultArr[(string)$doctor['ward_id']]['secondaryPerWriting']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['secondaryPerWriting']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['secondaryPerWriting']++;
				} else {
					$resultArr[(string)$doctor['ward_id']]['secondaryPerQueue']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['secondaryPerQueue']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['secondaryPerQueue']++;
				}
			}
			$resultArr[(string)$doctor['ward_id']]['numAllGreetings']++;
			$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['numAllGreetings']++;
			$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['numAllGreetings']++;
		}
        return $resultArr;
	}
	
	public function getMisStat($filters) {
		$connection = Yii::app()->db;
        $doctor = $connection->createCommand()
            ->select('d.*, w.name as ward, w.id as ward_id, m.name as post, m.id as post_id, dsbd.greeting_type, dsbd.patient_day, dsbd.order_number, dsbd.time_end, dsbd.is_accepted, mc.reg_date')
            ->from('mis.doctors d')
            ->leftJoin('mis.wards w', 'd.ward_code = w.id')
            ->leftJoin('mis.medpersonal m', 'd.post_id = m.id')
			->leftJoin(SheduleByDay::model()->tableName().' dsbd', 'dsbd.doctor_id = d.id')
			->leftJoin('mis.medcards mc', 'dsbd.medcard_id = mc.card_number');
		
		if($filters !== false) {
			$this->getSearchConditions($doctor, $filters, array(
			), array(
				  'd' => array('doctor_id', 'id'),
				  'm' => array('medworker_id', 'id'),
				  'w' => array('ward_id', 'id'),
				  'dsbd' => array('patient_day', 'patient_day_to', 'patient_day_from')
			), array(
				'doctor_id' => 'id',
				'medworker_id' => 'id',
				'ward_id' => 'id',
				'patient_day_to' => 'patient_day',
				'patient_day_from' => 'patient_day'
			));
		 }
		
		$doctor->andWhere('dsbd.time_begin IS NOT NULL');
		$doctor->andWhere('m.is_medworker = 1');
		
		$dateBegin = false;
		$dateEnd = false;
		foreach($filters['rules'] as $filter) {
			if($filter['field'] == 'patient_day_from' && trim($filter['data']) != '') {
				$dateBegin = $filter['data'];
			}
			if($filter['field'] == 'patient_day_to' && trim($filter['data']) != '') {
				$dateEnd = $filter['data'];
			}
		}
		
		$doctors = $doctor->queryAll();
		$resultArr = array();
		
		foreach($doctors as $doctor) {
			// Несуществующее отделение
			if(!isset($resultArr[(string)$doctor['ward_id']])) {
				$resultArr[(string)$doctor['ward_id']] = array(
					'name' => $doctor['ward'] != null ? $doctor['ward'] : 'Неизвестное отделение',
					'numAllGreetings' => 0,
					'closedGreetings' => 0,
					'handworkGreetings' => 0,
					'elements' => array()
				);
			}
			// Несуществующая специальность
			if(!isset($resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']])) {
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']] = array(
					'name' => $doctor['post'] != null ? $doctor['post'] : 'Неизвестная специальность',
					'numAllGreetings' => 0,
					'closedGreetings' => 0,
					'handworkGreetings' => 0,
					'elements' => array()
				);
			}
			
			// Несуществующий врач
			if(!isset($resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']])) {
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']] = array(
					'name' => $doctor['last_name'].' '.$doctor['first_name'].' '.$doctor['middle_name'],
					'data' => array(
						'numAllGreetings' => 0,
						'closedGreetings' => 0,
						'handworkGreetings' => 0
					)
				);
							
				// Считаем приёмы, которые добавили вручную, для данного врача
				$numFakes = TasuFakeGreetingsBuffer::model()->getNumRows($doctor['id'], $dateBegin, $dateEnd);
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['handworkGreetings'] = $numFakes['num_greetings'];
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['handworkGreetings'] += $numFakes['num_greetings'];
				$resultArr[(string)$doctor['ward_id']]['handworkGreetings'] += $numFakes['num_greetings'];
			}
			
			if($doctor['is_accepted'] == 1) { // Закрытый приём вручную
				$resultArr[(string)$doctor['ward_id']]['closedGreetings']++;
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['closedGreetings']++;
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['closedGreetings']++;
			} 
			
			$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['numAllGreetings']++;
			$resultArr[(string)$doctor['ward_id']]['numAllGreetings']++;
			$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['numAllGreetings']++;
		}
		
		return $resultArr;
	}
}

?>