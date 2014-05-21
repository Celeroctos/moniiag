<?php
class MonitoringType extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.monitoring_types';
    }

    public function primaryKey()
    {
        return 'id';
        // Для составного первичного ключа следует использовать массив:
        // return array('pk1', 'pk2');
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {

    }

}