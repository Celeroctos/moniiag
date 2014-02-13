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


    public function getRows($date, $doctorId, $withMediate = 1, $onlyForDoctor = 0) {
        $connection = Yii::app()->db;
        // Здесь есть анальный баг с повтором строк..
        $patients = $connection->createCommand()
            ->selectDistinct('dsbd.*, CONCAT(o.last_name, \' \', o.first_name, \' \', o.middle_name ) as fio, m.card_number AS card_number, SUBSTR(CAST(dsbd.patient_time AS text), 0, CHAR_LENGTH(CAST(dsbd.patient_time AS text)) - 2) AS patient_time, m.motion')
            ->from('mis.doctor_shedule_by_day dsbd')
            ->leftJoin('mis.medcards m', 'dsbd.medcard_id = m.card_number')
            ->leftJoin('mis.oms o', 'm.policy_id = o.id')
            ->join('mis.users u', 'u.employee_id = dsbd.doctor_id')
            ->where('dsbd.doctor_id = :doctor_id AND dsbd.patient_day = :patient_day', array(':patient_day' => $date, ':doctor_id' => $doctorId));
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
    public function getGreetingsPerQrit($patientId, $doctorId, $date = false, $status = 0) {
        try {
            $connection = Yii::app()->db;
            $greetings = $connection->createCommand()
                ->select('dsbd.*, o.first_name as p_first_name,
                                  o.middle_name as p_middle_name,
                                  o.last_name as p_last_name,
                                  d.first_name as d_first_name,
                                  d.middle_name as d_middle_name,
                                  d.last_name as d_last_name,
                                  m.motion,
                                  o.id as oms_id')
                ->from('mis.doctor_shedule_by_day dsbd')
                ->join('mis.medcards m', 'dsbd.medcard_id = m.card_number')
                ->join('mis.oms o', 'm.policy_id = o.id')
                ->join('mis.doctors d', 'd.id = dsbd.doctor_id');

            if(!is_array($patientId) && !is_array($doctorId)) {
                $greetings->where('o.id = :id AND dsbd.doctor_id = :doctor_id', array(':id' => $patientId, ':doctor_id' => $doctorId));
            } elseif(is_array($patientId) && count($patientId) > 0) {
                $greetings->where(array('IN', 'm.policy_id', $patientId));
            } elseif(is_array($doctorId) && count($doctorId) > 0) {
                $greetings->where(array('IN', 'dsbd.doctor_id', $doctorId));
            }

            if($date !== false) {
                $greetings->andWhere('dsbd.patient_day = :patient_day', array(':patient_day' => $date));
            }

            $greetings->order('dsbd.patient_time');
            $greetings->group('dsbd.id, dsbd.id, o.first_name, o.last_name, o.middle_name, d.first_name, d.last_name, d.middle_name, m.motion, o.id');

            return $greetings->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>