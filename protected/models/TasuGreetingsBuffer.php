<?php
class TasuGreetingsBuffer extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.tasu_greetings_buffer';
    }

    public function getLastBuffer($filters, $sidx = false, $sord = false, $start = false, $limit = false, $lastGreeting = false, $date = false, $doctorId = false, $importId = false) {
        try {
		    $connection = Yii::app()->db;
            $buffer = $connection->createCommand()
                ->select('tgb.*, CONCAT(o.last_name, \' \', o.first_name, \' \', o.middle_name ) as patient_fio, CONCAT(d.last_name, \' \', d.first_name, \' \', d.middle_name ) as doctor_fio, dsbd.patient_day, m.card_number as medcard, dsbd.is_beginned, dsbd.is_accepted, o.oms_number, o.id as oms_id, dsbd.doctor_id, p.mkb10_id as primary_diagnosis_id')
                ->from(TasuGreetingsBuffer::tableName().' tgb')
                ->leftJoin(SheduleByDay::tableName().' dsbd', 'tgb.greeting_id = dsbd.id')
                ->leftJoin(Medcard::tableName().' m', 'dsbd.medcard_id = m.card_number')
                ->leftJoin(Oms::tableName().' o', 'm.policy_id = o.id')
				->leftJoin(Doctor::tableName().' d', 'd.id = dsbd.doctor_id')
				->leftJoin(PatientDiagnosis::tableName().' p', 'p.greeting_id = tgb.greeting_id')
				->where('p.type = 0 OR tgb.fake_id IS NOT NULL OR NOT EXISTS(
					SELECT * 
					FROM '.PatientDiagnosis::tableName().' p2 
					WHERE greeting_id = tgb.greeting_id)');
			
			if($importId === false) {
				$buffer->andWhere('tgb.import_id = (SELECT DISTINCT MAX(tgb2.import_id) FROM '.TasuGreetingsBuffer::tableName().' tgb2)');
			} else {
				$buffer->andWhere('tgb.import_id = :import_id', array(':import_id' => $importId));
			}
			
			$buffer->andWhere('EXISTS(SELECT * FROM '.SheduleByDay::tableName().' dsbd2 WHERE dsbd2.id = dsbd.id) OR tgb.fake_id IS NOT NULL');
			
			if($importId === false) {
				$buffer->andWhere('tgb.status = 0'); // Получить всё то, что не выгружено
			}
            if($lastGreeting !== false) {
                $buffer->andWhere('tgb.id > :last_greeting', array(':last_greeting' => $lastGreeting));
            }

            if($filters !== false) {
                $this->getSearchConditions($buffer, $filters, array(
                ), array(
                ), array(
                ));
            }
			if($doctorId !== false) {
				$buffer->andWhere('d.id = :id OR tgb.fake_id IS NOT NULL', array(':id' => $doctorId));
			}
			if($date !== false) {
				$buffer->andWhere('dsbd.patient_day = :patient_day OR tgb.fake_id IS NOT NULL', array(':patient_day' => $date));
			}

            if($sidx !== false && $sord !== false) {
                $buffer->order($sidx.' '.$sord);
            }
            if($start !== false && $limit !== false && $doctorId === false) {
                $buffer->limit($limit, $start);

            }

			$bufferResult = $buffer->queryAll();
			$bufferAnswer = array();
			// 6891
			$counter = 0;
			$counterStart = 0;
            foreach($bufferResult as $key => &$bufferElement) {

                if($bufferElement['fake_id'] != null) {			
                    $fakeModel = TasuFakeGreetingsBuffer::model()->findByPk($bufferElement['fake_id']);
					
					if($doctorId !== false && $counterStart < $start && $start !== false) {
						if($fakeModel->doctor_id == $doctorId) {
							$counterStart++;
						}
						continue;
					}
					
					if($doctorId !== false && $fakeModel->doctor_id != $doctorId) {
						continue; 
					} elseif($limit !== false || $doctorId !== false) {
						$counter++;
					}
					
					if($fakeModel != null) {
						$fakeModelData = $connection->createCommand()
							->select('tfg.*, d.*, m.*, o.*, o.last_name as o_last_name, o.first_name as o_first_name, o.middle_name as o_middle_name, d.last_name as d_last_name, d.first_name as d_first_name, d.middle_name as d_middle_name, p.tasu_string as payment_type')
							->from(TasuFakeGreetingsBuffer::model()->tableName().' tfg')
							->leftJoin(Doctor::model()->tableName().' d', 'tfg.doctor_id = d.id')
							->leftJoin(Medcard::model()->tableName().' m', 'tfg.card_number = m.card_number')
							->leftJoin(Oms::model()->tableName().' o', 'm.policy_id = o.id')
							->leftJoin(Payment::model()->tableName().' p', 'p.id = tfg.payment_type')
							->where('tfg.doctor_id = :doctor_id
									AND tfg.card_number = :card_number', 
									array(
										':doctor_id' => $fakeModel['doctor_id'],
										':card_number' => $fakeModel['card_number']
									))
							->queryRow();
							
						$bufferElement['greeting_id'] = '-';
						$bufferElement['patient_fio'] = $fakeModelData['o_last_name'].' '.$fakeModelData['o_first_name'].' '.$fakeModelData['o_middle_name'];
						$bufferElement['doctor_fio'] = $fakeModelData['d_last_name'].' '.$fakeModelData['d_first_name'].' '.$fakeModelData['d_middle_name'];
						$bufferElement['patient_day'] = $fakeModelData['greeting_date'];
						$bufferElement['medcard'] = $fakeModelData['card_number'];
						$bufferElement['is_beginned'] = $bufferElement['is_accepted'] = 1;
						$bufferElement['oms_number'] = $fakeModelData['oms_number'];
						$bufferElement['oms_id'] = $fakeModelData['policy_id'];
						$bufferElement['doctor_id'] = $fakeModel['doctor_id'];
						$bufferElement['primary_diagnosis_id'] = $fakeModel['primary_diagnosis_id'];
						$bufferElement['payment_type'] = $fakeModel['payment_type'];
					}
                } else {
					$counter++;
				}
				
				if($limit !== false && $counter > $limit) {
					break;
				}
				
				$bufferAnswer[] = $bufferElement;
            }

            return $bufferAnswer;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getLastImportId() {
        try {
            $connection = Yii::app()->db;
            $lastBufferId = $connection->createCommand()
                ->select('MAX(tgb.import_id) as max_import_id')
                ->from(TasuGreetingsBuffer::tableName().' tgb')
                ->queryRow();

            if($lastBufferId['max_import_id'] == null || $lastBufferId['max_import_id'] == 0) {
                $lastBufferId['max_import_id'] = 1;
            } else {
                // Проверим, не закрыта ли данная выгрузка. Состояние "отменённая выгрузка" (2) не считается.
                $issetInHistory = TasuGreetingsBufferHistory::model()->find('import_id = :import_id AND status = 1', array(':import_id' => $lastBufferId['max_import_id']));
                if($issetInHistory != null) {
					// Посмотрим, остались ли невыгруженные строки-приёмы (ошибки)
					$numErrors = $connection->createCommand()
						->select('COUNT(*) as num')
						->from(TasuGreetingsBuffer::tableName().' tgb')
						->where('import_id = :import_id AND status = 0', array(':import_id' => $lastBufferId['max_import_id']))
						->queryRow();
					
					if($numErrors['num'] == 0) {
						$lastBufferId['max_import_id']++;
					}
                }
			}

            return $lastBufferId;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getAllNotBuffered() {
        try {
            $connection = Yii::app()->db;
            $notBuffered = $connection->createCommand()
                ->select('dsbd.*, CONCAT(o.last_name, \' \', o.first_name, \' \', o.middle_name ) as patient_fio, CONCAT(d.last_name, \' \', d.first_name, \' \', d.middle_name ) as doctor_fio, dsbd.patient_day, m.card_number as medcard')
                ->from(SheduleByDay::tableName().' dsbd')
                ->join(Medcard::tableName().' m', 'dsbd.medcard_id = m.card_number')
                ->join(Oms::tableName().' o', 'm.policy_id = o.id')
                ->join(Doctor::tableName().' d', 'd.id = dsbd.doctor_id')
                ->where('NOT EXISTS (SELECT *
                                     FROM '.TasuGreetingsBuffer::tableName().' tgb
                                     WHERE tgb.greeting_id = dsbd.id)')
				->andWhere('dsbd.is_accepted = 1');

            return $notBuffered->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>