<?php
class CladrSettlement extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.cladr_settlements';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $settlements = $connection->createCommand()
            ->select('cs.*, cr.id as region_id, cr.name as region, cs.name as district')
            ->from(CladrSettlement::tableName().' cs')
            ->leftJoin(CladrRegion::tableName(). ' cr', 'cs.code_region = cr.code_cladr');

        if($filters !== false) {
            $this->getSearchConditions($settlements, $filters, array(
            ), array(
                'cs' => array('name', 'code_region', 'code_district')
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false) {
            $settlements->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $settlements->limit($limit, $start);
        }

        return $settlements->queryAll();

    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $settlement = $connection->createCommand()
                ->select('cs.*')
                ->from(CladrSettlement::tableName().' cs')
                ->where('cs.id = :id', array(':id' => $id))
                ->queryRow();

            return $settlement;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>