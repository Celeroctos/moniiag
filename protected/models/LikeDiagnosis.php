<?php
class LikeDiagnosis extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.like_diagnosis';
    }

    public function getAll() {


    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {

    }

    public function getOne($medworkerId) {
        $connection = Yii::app()->db;
        $medworker = $connection->createCommand()
            ->select('ld.*, m.*')
            ->from('mis.like_diagnosis ld')
            ->join('mis.mkb10 m', 'm.id = ld.mkb_id')
            ->where('ld.medworker_id = :medworker_id', array(':medworker_id' => $medworkerId));

        return $medworker->queryAll();

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

}