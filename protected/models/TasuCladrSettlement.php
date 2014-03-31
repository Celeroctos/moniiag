<?php
class TasuCladrSettlement extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'dbo.t_settlement_30932';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = $this->getDbConnection();
        $settlements = $connection->createCommand()
            ->select('tcs.*')
            ->from(TasuCladrSettlement::tableName().' tcs');

        if($filters !== false) {
            $this->getSearchConditions($settlements, $filters, array(
            ), array(
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false ) {
            $settlements->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $settlements->limit($limit, $start);
        }

        return $settlements->queryAll();
    }
}

?>