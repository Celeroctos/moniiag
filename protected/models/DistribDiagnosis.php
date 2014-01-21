<?php
class DistribDiagnosis extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.mkb10_distrib';
    }

    public function getRows($filters, $medworkerId, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $mkb10 = $connection->createCommand()
            ->select('md.*, m.*')
            ->from($this->tableName().' md')
            ->join(Mkb10::tableName().' m', 'm.id = md.mkb10_id')
            ->where('md.medworker_id = :medworker_id', array(':medworker_id' => $medworkerId));

        if($filters !== false) {
            $this->getSearchConditions($mkb10, $filters, array(

            ), array(
                'm' => array('id', 'description')
            ), array(

            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $mkb10->order($sidx.' '.$sord);
            $mkb10->limit($limit, $start);
        }

        return $mkb10->queryAll();
    }

    public function getOne($medworkerId) {
        $connection = Yii::app()->db;
        $medworker = $connection->createCommand()
            ->select('ld.*, m.*')
            ->from($this->tableName().' ld')
            ->join('mis.mkb10 m', 'm.id = ld.mkb_id')
            ->where('ld.medworker_id = :medworker_id', array(':medworker_id' => $medworkerId));

        return $medworker->queryAll();
    }

}