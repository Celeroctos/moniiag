<?php
class CladrDistrict extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.cladr_districts';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false,$nullCodeCladrShow=true) {
        $connection = Yii::app()->db;
        $districts = $connection->createCommand()
            ->select('cd.*, cr.id as region_id, cr.name as region')
            ->from(CladrDistrict::model()->tableName().' cd')
            ->leftJoin(CladrRegion::model()->tableName(). ' cr', 'cd.code_region = cr.code_cladr');
           // ->where('code_cladr!=\'000\'');

        if (!$nullCodeCladrShow)
        {
            $districts = $districts -> andWhere ( 'not (cd.code_cladr=\'000\')', array()  );
        }

        if($filters !== false) {
            $this->getSearchConditions($districts, $filters, array(
            ), array(
                'cd' =>  array('name', 'code_region')
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false) {
            $districts->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $districts->limit($limit, $start);
        }

        return $districts->queryAll();

    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $district = $connection->createCommand()
                ->select('cd.*, cr.name as region, cr.id as region_id')
                ->from(CladrDistrict::model()->tableName().' cd')
                ->leftJoin(CladrRegion::model()->tableName().' cr', 'cr.code_cladr = cd.code_region')
                ->where('cd.id = :id', array(':id' => $id))
                ->queryRow();

            return $district;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>