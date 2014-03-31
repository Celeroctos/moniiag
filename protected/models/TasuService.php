<?php
class TasuService extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'PDPStdStorage.dbo.t_medicalservice_02852';
    }

    public function getRows($filters, $version, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = $this->getDbConnection();
        $services = $connection->createCommand()
            ->select('tm.*')
            ->from(TasuService::tableName().' tm')
            ->where("tm.version_end = :version", array(':version' => $version));

        if($filters !== false) {
            $this->getSearchConditions($services, $filters, array(
            ), array(
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false ) {
            $services->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $services->limit($limit, $start);
        }

        return $services->queryAll();
    }
}

?>