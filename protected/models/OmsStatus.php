<?php
class OmsStatus extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.oms_statuses';
    }

    public function primaryKey()
    {
        return 'id';
        // Для составного первичного ключа следует использовать массив:
        // return array('pk1', 'pk2');
    }

    public static function getForSelect ()
    {
        $omsStatusObject = new OmsStatus();
        $omsStatuses = $omsStatusObject->getRows(false);
        $result = array();
        foreach($omsStatuses as $oneStatus)
        {
            $result[$oneStatus['id']] = $oneStatus['tasu_id'].': '.$oneStatus['name'];
        }
        return $result;
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $statuses = $connection->createCommand()
            ->select('os.*')
            ->from('mis.oms_statuses os');

        if($filters !== false) {
            $this->getSearchConditions($statuses, $filters, array(
            ), array(
                'os' => array('id', 'name')
            ), array(

            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $statuses->order($sidx.' '.$sord);
            $statuses->limit($limit, $start);
        }

        return $statuses->queryAll();

    }
}
?>