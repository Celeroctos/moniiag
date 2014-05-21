<?php
class MonitoringOms extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.monitoring_oms';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $settings = $connection->createCommand()
            ->select('mo.*, o.*, mt.*')
            ->from('mis.monitoring_oms mo')
            ->join('mis.oms o', 'mo.id_patient= o.id')
            ->join('mis.monitoring_types mt', 'mt.id= mo.monitoring_type');

        return $settings->queryAll();
    }

}