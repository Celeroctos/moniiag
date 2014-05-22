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
            ->select('mo.id as monitoring_id, mt.name, o.first_name,o.middle_name,o.last_name,
            (SELECT COUNT (*) FROM mis.remote_data rd WHERE rd.id_monitoring = mo.id
                AND (    (rd.is_read=0) AND (( monitoring_type=1 AND CAST(indicator_value AS float8)>220  ) OR ( monitoring_type=2 AND CAST(indicator_value AS float8)>30 ))    )
             ) need_look')
            ->from('mis.monitoring_oms mo')
            ->join('mis.oms o', 'mo.id_patient= o.id')
            ->join('mis.monitoring_types mt', 'mt.id= mo.monitoring_type');

        return $settings->queryAll();
    }

}