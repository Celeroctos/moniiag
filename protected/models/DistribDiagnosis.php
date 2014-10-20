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

    public function getRows($filters, $employeeId, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $mkb10 = $connection->createCommand()
            ->select('md.*, cd.*')
            ->from(DistribDiagnosis::model()->tableName().' md')
            ->join(ClinicalDiagnosis::model()->tableName().' cd', 'cd.id = md.mkb10_id')
            ->where('md.employee_id = :employee_id', array(':employee_id' => $employeeId));

        if($filters !== false) {
            $this->getSearchConditions($mkb10, $filters, array(
            ), array(
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false) {
            $mkb10->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $mkb10->limit($limit, $start);
        }

        return $mkb10->queryAll();
    }

    public function getOne($employeeId) {
        $connection = Yii::app()->db;
        $medworker = $connection->createCommand()
            ->select('md.*, cd.*')
            ->from(DistribDiagnosis::model()->tableName().' md')
            ->join(ClinicalDiagnosis::model()->tableName().' cd', 'cd.id = md.mkb_id')
            ->where('md.employee_id = :employee_id', array(':employee_id' => $employeeId));

        return $medworker->queryAll();
    }

}