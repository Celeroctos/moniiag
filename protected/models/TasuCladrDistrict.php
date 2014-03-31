<?php
class TasuCladrDistrict extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'dbo.t_district_21233';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = $this->getDbConnection();
        $disctricts = $connection->createCommand()
            ->select('tcr.*')
            ->from(TasuCladrDistrict::tableName().' tcr');

        if($filters !== false) {
            $this->getSearchConditions($disctricts, $filters, array(
            ), array(
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false ) {
            $disctricts->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $disctricts->limit($limit, $start);
        }

        return $disctricts->queryAll();
    }
}

?>