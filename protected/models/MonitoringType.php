<?php
class MonitoringType extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.monitoring_type';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {

    }

}