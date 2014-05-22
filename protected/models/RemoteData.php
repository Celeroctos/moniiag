<?php
class RemoteData extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.remote_data';
    }

    public function primaryKey()
    {
        return 'id';
        // Для составного первичного ключа следует использовать массив:
        // return array('pk1', 'pk2');
    }

    public function getAlarms()
    {
        $threshHolds = array();

        // Пусть будут такие пороги
        // 1 - 220 (давление)
        // 2 - 30(сахар)


        $connection = Yii::app()->db;
        $indicators = $connection->createCommand()
            ->select(' COUNT (*) ')
            ->from('mis.remote_data rd')
            ->leftJoin('mis.monitoring_oms mo', 'rd.id_monitoring=mo.id')
            ->where(
                '(
                        (rd.is_read=0)
                        AND
                        (
                            (
                                monitoring_type=1 AND
                                (CAST(indicator_value AS float8)>140 OR CAST(indicator_value AS float8)<70)
                            )
                            OR
                            (
                                monitoring_type=2 AND
                                (CAST(indicator_value AS float8)>7 OR  CAST(indicator_value AS float8)<4)
                            )
                        )
                  )'
                , array())
            ->queryRow();

        return $indicators;


    }

}