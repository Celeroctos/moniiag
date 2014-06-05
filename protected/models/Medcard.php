<?php
class Medcard extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcards';
    }

    public function getAll() {


    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false, $enterpriseId = false, $wardId = false, $employeeId = false) {

    }

    // Получить историю движения медкарты
    public function getHistoryOfMotion($omsId, $sidx = false, $sord = false, $start = false, $limit = false)
    {
        $connection = Yii::app()->db;
        /*Соединяем
        1. ОМСы
        2. Медкарты
        3. Таблица распределения пацентов по времени
        4. Докторов
        5. Связь докторов и кабинетов
        6. Кабинеты
        
        Затем группируем это всё */
            $result = $connection->createCommand()
            ->select(   '(dsd.patient_day || \' \' || dsd.patient_time) as greeting_timestamp,
                        dsd.medcard_id,
                        (d.first_name || \' \' ||  d.middle_name  || \' \' || d.last_name) as doctor_name,
                        c.cab_number') // 
            ->from('mis.doctor_shedule_by_day dsd')
            ->join('mis.medcards mc', 'mc.card_number = dsd.medcard_id')
            ->join('mis.oms policy', 'mc.policy_id = policy.id')
            ->join('mis.doctors d', 'd.id = dsd.doctor_id')
            ->join('mis.doctor_cabinet cd', 'cd.doctor_id = d.id')
            ->join('mis.cabinets c', 'cd.cabinet_id = c.id')
            ->where('policy.id = :oms_id', array(':oms_id' => $omsId))
            ->group(array('dsd.id','greeting_timestamp','dsd.medcard_id','doctor_name', 'c.cab_number'));

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false)
        {
            $result->order($sidx.' '.$sord);
            $result->limit($limit, $start);
        }    
            
        return $result->queryAll();
    }
    
    public function getOne($cardNumber) {
        $connection = Yii::app()->db;
        $medcard = $connection->createCommand()
            ->select('m.*, o.*')
            ->from('mis.medcards m')
            ->join('mis.oms o', 'm.policy_id = o.id')
            ->where('m.card_number = :card_number', array(':card_number' => $cardNumber));

        return $medcard->queryRow();

    }

    public function getLastMedcardPerYear($code, $patientId = null) {
        $connection = Yii::app()->db;
        $medcard = $connection->createCommand()
            ->select('m.*, CAST(SUBSTRING("m"."card_number", 0, (CHAR_LENGTH("m"."card_number") - 2)) as INTEGER) as "fx"') // Выделение части ключа: нужно отсутствие суррогатного ключа
            ->from('mis.medcards m');
        if($patientId != null) {
            $medcard->join('mis.oms o', 'o.id = m.policy_id')
                    ->where('o.id = :id', array(':id' => $patientId));
        }
        $medcard->andWhere(array('like', 'm.card_number', '%/'.$code))
                ->order('fx desc')
                ->limit(1, 0);

        return $medcard->queryAll();
    }

    public function getLastByPatient($patientId) {
        $connection = Yii::app()->db;
        $medcard = $connection->createCommand()
            ->select('m.*, CAST(SUBSTRING("m"."card_number", (CHAR_LENGTH("m"."card_number") - 1)) as INTEGER) as "fx"') // Выделение части ключа: нужно отсутствие суррогатного ключа
            ->from('mis.medcards m')
            ->leftJoin('mis.oms o', 'm.policy_id = o.id')
            ->where('o.id = :patient_id', array(':patient_id' => $patientId))
            ->order('fx desc')
            ->limit(1, 0);

        return $medcard->queryRow();
    }

    public function findByIds($ids) {
        $connection = Yii::app()->db;
        $medcard = $connection->createCommand()
            ->select('m.*')
            ->from('mis.medcards m')
            ->where(array('in', 'm.card_number', $ids))
            ->order('m.card_number');
        return $medcard->queryAll();
    }

    public function getTestOmsWithCards() {
        $connection = Yii::app()->db;
        $medcards = $connection->createCommand()
            ->select('m.*, o.*')
            ->from('mis.medcards m')
            ->leftJoin('mis.oms o', 'm.policy_id = o.id')
            ->where('o.tasu_id IS NULL');
        return $medcards->queryAll();
    }

}