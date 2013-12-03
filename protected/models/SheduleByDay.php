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


    public function getRows($date, $doctorId) {
        $connection = Yii::app()->db;
        $patients = $connection->createCommand()
            ->select('dsbd.*, CONCAT(o.last_name, \' \', o.first_name, \' \', o.middle_name ) as fio')
            ->from('mis.doctor_shedule_by_day dsbd')
            ->leftJoin('mis.medcards m', 'dsbd.medcard_id = m.card_number')
            ->leftJoin('mis.oms o', 'm.policy_id = o.id')
            ->where('dsbd.doctor_id = :doctor_id AND dsbd.patient_time = :patient_time', array(':patient_time' => $date, ':doctor_id' => $doctorId));
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
            ->selectDistinct('dsbd.patient_time')
            ->from('mis.doctor_shedule_by_day dsbd')
            ->leftJoin('mis.users u', 'dsbd.doctor_id = u.id')
            ->where('u.id = :id', array(':id' => $userId));
        return $dates->queryAll();
    }
}

?>