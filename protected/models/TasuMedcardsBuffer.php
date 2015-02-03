<?php
class TasuMedcardsBuffer extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.tasu_medcards_buffer';
    }

    public function getLastBuffer($filters, $sidx = false, $sord = false, $start = false, $limit = false, $importId = false, $lastMedcard = false) {
        try {
		    $connection = Yii::app()->db;
            $buffer = $connection->createCommand()
                ->select("tmb.*,
                          tmb.id as buffer_id,
                          CONCAT(o.last_name, ' ', o.first_name, ' ', o.middle_name ) as patient_fio,
                          m.*,
                          o.*,
                          CONCAT(m.serie, ' ', m.docnumber) as docdata,
                          os.name as status,
                          i.name as insurance")
                ->from(TasuMedcardsBuffer::model()->tableName().' tmb')
                ->leftJoin(Medcard::model()->tableName().' m', 'tmb.medcard = m.card_number')
                ->leftJoin(Oms::model()->tableName().' o', 'm.policy_id = o.id')
                ->leftJoin(OmsStatus::model()->tableName().' os', 'os.id = o.status')
                ->leftJoin(Insurance::model()->tableName().' i', 'i.id = o.insurance');
			
			if($importId === false) {
				$buffer->andWhere('tmb.import_id = (SELECT DISTINCT MAX(tmb2.import_id) FROM '.TasuMedcardsBuffer::model()->tableName().' tmb2)');
			} else {
				$buffer->andWhere('tmb.import_id = :import_id', array(':import_id' => $importId));
			}
			
			if($importId === false) {
				$buffer->andWhere('tmb.status = 0'); // Получить всё то, что не выгружено
			}
            if($lastMedcard !== false) {
                $buffer->andWhere('tmb.id > :last_medcard', array(':last_medcard' => $lastMedcard));
            }

            if($filters !== false) {
                $this->getSearchConditions($buffer, $filters, array(
                ), array(
                    'm' => array('date_from', 'date_to')
                ), array(
                    'date_from' => 'reg_date',
                    'date_to' => 'reg_date'
                ));
            }

            if($sidx !== false && $sord !== false) {
                $buffer->order($sidx.' '.$sord);
            }
            if($start !== false && $limit !== false) {
                $buffer->limit($limit, $start);

            }

			return $buffer->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getLastImportId() {
        try {
            $connection = Yii::app()->db;
            $lastBufferId = $connection->createCommand()
                ->select('MAX(tmb.import_id) as max_import_id')
                ->from(TasuMedcardsBuffer::model()->tableName().' tmb')
                ->queryRow();

            if($lastBufferId['max_import_id'] == null || $lastBufferId['max_import_id'] == 0) {
                $lastBufferId['max_import_id'] = 1;
            } else {
                // Проверим, не закрыта ли данная выгрузка. Состояние "отменённая выгрузка" (2) не считается.
                $issetInHistory = TasuMedcardsBufferHistory::model()->find('import_id = :import_id AND status = 1', array(':import_id' => $lastBufferId['max_import_id']));
                if($issetInHistory != null) {
					// Посмотрим, остались ли невыгруженные строки-приёмы (ошибки)
					$numErrors = $connection->createCommand()
						->select('COUNT(*) as num')
						->from(TasuMedcardsBuffer::model()->tableName().' tgb')
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
                ->from(SheduleByDay::model()->tableName().' dsbd')
                ->join(Medcard::model()->tableName().' m', 'dsbd.medcard_id = m.card_number')
                ->join(Oms::model()->tableName().' o', 'm.policy_id = o.id')
                ->join(Doctor::model()->tableName().' d', 'd.id = dsbd.doctor_id')
                ->where('NOT EXISTS (SELECT *
                                     FROM '.TasuGreetingsBuffer::model()->tableName().' tgb
                                     WHERE tgb.greeting_id = dsbd.id)')
				->andWhere('dsbd.is_accepted = 1');

            return $notBuffered->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>