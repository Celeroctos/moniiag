<?php
class TasuCladrStreet extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'dbo.t_street_48326';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = $this->getDbConnection();
        $streets = $connection->createCommand()
            ->select('tcst.*')
            ->from(TasuCladrStreet::tableName().' tcst');

        if($filters !== false) {
            $this->getSearchConditions($streets, $filters, array(
            ), array(
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false ) {
            $streets->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $streets->limit($limit, $start);
        }

        return $streets->queryAll();
    }

    public function getNumRows() {
        $connection = $this->getDbConnection();
        $numStreets = $connection->createCommand()
            ->select('COUNT(*) as num')
            ->from(TasuCladrStreet::tableName().' tcst')
            ->queryRow();
        return $numStreets['num'];
    }
}

?>