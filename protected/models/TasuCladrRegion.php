<?php
class TasuCladrRegion extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'dbo.t_region_24655';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = $this->getDbConnection();
        $regions = $connection->createCommand()
            ->select('tcr.*')
            ->from(TasuCladrRegion::tableName().' tcr');

        if($filters !== false) {
            $this->getSearchConditions($regions, $filters, array(
            ), array(
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false ) {
            $regions->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $regions->limit($limit, $start);
        }

        return $regions->queryAll();
    }
}

?>