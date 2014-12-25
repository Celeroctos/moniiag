<?php
class MediatePatient extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.mediate_patients';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $mediate = $connection->createCommand()
            ->select('mp.*')
            ->from(MediatePatient::model()->tableName().' mp');

        if($filters !== false) {
            $this->getSearchConditions($mediate, $filters, array(
                'fio' => array(
                    'first_name',
                    'last_name',
                    'middle_name'
                )
            ), array(
                'mp' => array('id', 'first_name', 'last_name', 'middle_name', 'phone')
            ), array(

            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $mediate->order($sidx.' '.$sord);
            $mediate->limit($limit, $start);
        }

        return $mediate->queryAll();

    }

    // Получить список опосредованных приёмов по критериям
    // status - только с медкартами, только без медкарт, все
    public function getGreetingsPerQrit($patientId, $doctorId, $date = false) {
        try {
            $connection = Yii::app()->db;
            $greetings = $connection->createCommand()
                ->select('dsbd.*, mp.first_name as p_first_name,
                                  mp.middle_name as p_middle_name,
                                  mp.last_name as p_last_name,
                                  mp.phone,
                                  d.first_name as d_first_name,
                                  d.middle_name as d_middle_name,
                                  d.last_name as d_last_name')
                ->from('mis.doctor_shedule_by_day dsbd')
                ->join('mis.doctors d', 'd.id = dsbd.doctor_id')
                ->join('mis.mediate_patients mp', 'dsbd.mediate_id = mp.id');

            if(is_array($doctorId) && count($doctorId) > 0) {
                $greetings->where(array('IN', 'd.id', $doctorId));
            }

            if($date !== false) {
                $greetings->andWhere('dsbd.patient_day = :patient_day', array(':patient_day' => $date));
            }

            $greetings->order('dsbd.patient_time');
            $greetings->group('dsbd.doctor_id, dsbd.id, mp.first_name, mp.last_name, mp.middle_name, d.first_name, d.last_name, d.middle_name, mp.phone');

            return $greetings->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getOne($id) {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>