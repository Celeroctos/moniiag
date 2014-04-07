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

    public function getLastBuffer($filters, $sidx = false, $sord = false, $start = false, $limit = false, $lastGreeting = false) {
        try {
            $connection = Yii::app()->db;
            $buffer = $connection->createCommand()
                ->select('tgb.*, CONCAT(o.last_name, \' \', o.first_name, \' \', o.middle_name ) as patient_fio, CONCAT(d.last_name, \' \', d.first_name, \' \', d.middle_name ) as doctor_fio, dsbd.patient_day, m.card_number as medcard, dsbd.is_beginned, dsbd.is_accepted, o.oms_number, o.id as oms_id, dsbd.doctor_id')
                ->from(TasuGreetingsBuffer::tableName().' tgb')
                ->join(SheduleByDay::tableName().' dsbd', 'tgb.greeting_id = dsbd.id')
                ->join(Medcard::tableName().' m', 'dsbd.medcard_id = m.card_number')
                ->join(Oms::tableName().' o', 'm.policy_id = o.id')
                ->join(Doctor::tableName().' d', 'd.id = dsbd.doctor_id')
                ->where('tgb.import_id = (SELECT DISTINCT MAX(tgb2.import_id)
                                          FROM '.TasuGreetingsBuffer::tableName().' tgb2)')
                ->andWhere('tgb.status = 0'); // Получить всё то, что не выгружено
            if($lastGreeting !== false) {
                $buffer->andWhere('tgb.id > :last_greeting', array(':last_greeting' => $lastGreeting));
            }

            if($filters !== false) {
                $this->getSearchConditions($buffer, $filters, array(
                ), array(
                ), array(
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
                ->select('MAX(tgb.import_id) as max_import_id')
                ->from(TasuGreetingsBuffer::tableName().' tgb')
                ->queryRow();

            if($lastBufferId['max_import_id'] == null || $lastBufferId['max_import_id'] == 0) {
                $lastBufferId['max_import_id'] = 1;
            } else {
                // Проверим, не закрыта ли данная выгрузка
                $issetInHistory = TasuGreetingsBufferHistory::model()->find('import_id = :import_id', array(':import_id' => $lastBufferId['max_import_id']));
                if($issetInHistory != null) {
                    $lastBufferId['max_import_id']++;
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
				->andWhere('dsbd.is_accepted = 1 AND dsbd.id = 457');

            return $notBuffered->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>