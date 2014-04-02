<?php
class CladrStreet extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.cladr_streets';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $streets = $connection->createCommand()
            ->select('cst.*, cr.id as region_id, cr.name as region')
            ->from(CladrStreet::tableName().' cst')
            ->leftJoin(CladrRegion::tableName(). ' cr', 'cst.code_region = cr.code_cladr');

        if($filters !== false) {
            $this->getSearchConditions($streets, $filters, array(
            ), array(
                'cst' => array('name', 'code_region', 'code_district', 'code_settlement')
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false) {
            $streets->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $streets->limit($limit, $start);
        }

        return $streets->queryAll();

    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $street = $connection->createCommand()
                ->select('cst.*')
                ->from(CladrStreet::tableName().' cst')
                ->where('cst.id = :id', array(':id' => $id))
                ->queryRow();

            return $street;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>