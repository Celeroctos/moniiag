<?php
class CladrRegion extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.cladr_regions';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $regions = $connection->createCommand()
            ->select('cr.*')
            ->from(CladrRegion::tableName().' cr');

        if($filters !== false) {
            $this->getSearchConditions($regions, $filters, array(
            ), array(
                'cr' => array('name')
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false) {
            $regions->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $regions->limit($limit, $start);
        }

        return $regions->queryAll();

    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $region = $connection->createCommand()
                ->select('cr.*')
                ->from(CladrRegion::tableName().' cr')
                ->where('cr.id = :id', array(':id' => $id))
                ->queryRow();

            return $region;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>