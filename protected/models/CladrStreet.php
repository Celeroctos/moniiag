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
                ->select('cst.*, cr.name as region, cr.id as region_id, cd.id as district_id, cd.name as district, cr.name as region, cs.id as settlement_id, cs.name settlement')
                ->from(CladrStreet::tableName().' cst')
                ->leftJoin(CladrRegion::tableName().' cr', 'cr.code_cladr = cst.code_region')
                ->leftJoin(CladrDistrict::tableName().' cd', 'cd.code_cladr = cst.code_district')
                ->leftJoin(CladrSettlement::tableName().' cs', 'cs.code_cladr = cst.code_settlement')
                ->where('cst.id = :id
                            AND cd.id IN(SELECT cd2.id
                                     FROM '.CladrDistrict::tableName().' cd2
                                     WHERE cd2.code_cladr = cd.code_cladr
                                        AND cd2.code_region = cr.code_cladr)
                            AND cs.id IN(SELECT cs2.id
                                     FROM '.CladrSettlement::tableName().' cs2
                                     WHERE cs2.code_cladr = cs.code_cladr
                                        AND cs2.code_region = cr.code_cladr
                                        AND cs2.code_district = cd.code_cladr)', array(':id' => $id))
                ->queryRow();

            return $street;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getNumRows($filters) {
        try {
            $connection = Yii::app()->db;
            $num = $connection->createCommand()
                ->select('COUNT(cst.*) as num')
                ->from(CladrStreet::tableName().' cst');

            if($filters !== false) {
                $this->getSearchConditions($num, $filters, array(
                ), array(
                    'cst' => array('name', 'code_region', 'code_district', 'code_settlement')
                ), array(
                ));
            }

            $row = $num->queryRow();
            return $row['num'];

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>