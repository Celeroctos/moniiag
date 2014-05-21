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


    public function primaryKey()
    {
        return 'id';
        // Для составного первичного ключа следует использовать массив:
        // return array('pk1', 'pk2');
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $settings = $connection->createCommand()
            ->select('mo.*, mo.id as monitoring_id, o.*, mt.*')
            ->from('mis.monitoring_oms mo')
            ->join('mis.oms o', 'mo.id_patient= o.id')
            ->join('mis.monitoring_types mt', 'mt.id= mo.monitoring_type');

        return $settings->queryAll();
    }

}