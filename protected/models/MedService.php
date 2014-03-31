<?php
class MedService extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medservices';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $service = $connection->createCommand()
                ->select('m.*')
                ->from(MedService::tableName().' m')
                ->where('m.id = :id', array(':id' => $id))
                ->queryRow();

            return $service;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $services = $connection->createCommand()
            ->select('m.*')
            ->from(MedService::tableName().' m');

        if($filters !== false) {
            $this->getSearchConditions($services, $filters, array(

            ), array(
                'm' => array('id', 'name', 'code')
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