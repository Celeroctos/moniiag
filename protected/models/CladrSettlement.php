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

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false,$nullCodeCladrShow=true) {
        try
        {
            $connection = Yii::app()->db;
            $settlements = $connection->createCommand()
                ->select('cs.*, cr.id as region_id, cr.name as region')
                ->from(CladrSettlement::tableName().' cs')
                ->leftJoin(CladrRegion::tableName(). ' cr', 'cs.code_region = cr.code_cladr');

            if($filters !== false) {
                $this->getSearchConditions($settlements, $filters, array(
                ), array(
                    'cs' => array('name', 'code_region', 'code_district')
                ), array(
                ));
            }

           if (!$nullCodeCladrShow)
            {
                $settlements =  $settlements -> andWhere ( 'not (cs.code_cladr=\'000000\')', array()  );
            }

            if($sidx !== false && $sord !== false) {
                $settlements->order($sidx.' '.$sord);
            }
            if($start !== false && $limit !== false) {
                $settlements->limit($limit, $start);
            }

           // var_dump($settlements);
           // exit();
            $result = $settlements->queryAll();
           // var_dump("!");
           // exit();
            foreach($result as &$element) {
                $district = CladrDistrict::model()->find(
                  'code_cladr = :code_cladr
                  AND code_region = :code_region',
                  array(':code_cladr' => $element['code_district'],
                        ':code_region' => $element['code_region'])
                );

                if($district != null) {
                    $element['district_id'] = $district->id;
                    $element['district'] = $district->name;
                } else {
                    $element['district_id'] = null;
                    $element['district'] = null;
                }
            }
            return $result;
        } catch(Exception $e) {
            echo $e->getMessage();
            exit();
        }

    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $settlement = $connection->createCommand()
                ->select('cs.*, cr.name as region, cr.id as region_id, cd.id as district_id, cd.name as district, cr.name as region')
                ->from(CladrSettlement::tableName().' cs')
                ->leftJoin(CladrRegion::tableName().' cr', 'cr.code_cladr = cs.code_region')
                ->leftJoin(CladrDistrict::tableName().' cd', 'cd.code_cladr = cs.code_district')
                ->where('cs.id = :id AND cd.id IN(SELECT cd2.id FROM '.CladrDistrict::tableName().' cd2 WHERE cd2.code_cladr = cd.code_cladr AND cd2.code_region = cr.code_cladr)', array(':id' => $id))
                ->queryRow();

            return $settlement;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getNumRows($filters,$nullCodeCladrShow=true) {
        try {
            $connection = Yii::app()->db;
            $num = $connection->createCommand()
                ->select('COUNT(cs.*) as num')
                ->from(CladrSettlement::tableName().' cs');


            if($filters !== false) {
                $this->getSearchConditions($num, $filters, array(
                ), array(
                    'cs' => array('name', 'code_region', 'code_district')
                ), array(
                ));
            }

            if (!$nullCodeCladrShow)
            {
                $num -> andWhere ( 'not (cs.code_cladr=\'000000\')', array()  );
            }

            $row = $num->queryRow();
            return $row['num'];

        } catch(Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }
}

?>