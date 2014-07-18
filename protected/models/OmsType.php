<?php
class OmsType extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.oms_types';
    }

    public function primaryKey()
    {
        return 'id';
        // Для составного первичного ключа следует использовать массив:
        // return array('pk1', 'pk2');
    }

    public static function getForSelect ()
    {
        $omsTypeObject = new OmsType();
        $omsTypes = $omsTypeObject->getRows(false);
        $result = array();
        foreach($omsTypes as $oneType)
        {
            $result[$oneType['id']] = $oneType['tasu_id'].': '.$oneType['name'];
        }
        return $result;
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $types = $connection->createCommand()
            ->select('ot.*')
            ->from('mis.oms_types ot');

        if($filters !== false) {
            $this->getSearchConditions($types, $filters, array(
            ), array(
                'ot' => array('id', 'name')
            ), array(

            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $types->order($sidx.' '.$sord);
            $types->limit($limit, $start);
        }

        return $types->queryAll();

    }
}
?>