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
            ->from(CladrRegion::model()->tableName().' cr');

        if($filters !== false) {
            $this->getSearchConditions($regions, $filters, array(
            ), array(
                'cr' => array('name','code_cladr')
            ), array(
            ));
        }

        // ���� ���� ������ �� ���� name - �� ��������� ������� or where �� ��� �����
        if (isset ($filters['rules'][0]['field']))
        {
            if ($filters['rules'][0]['field']=='name')
            {
                $regions->orWhere(array('like', 'LOWER(cr.code_cladr)', '%'.$filters['rules'][0]['data'].'%'));
            }
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
                ->from(CladrRegion::model()->tableName().' cr')
                ->where('cr.id = :id', array(':id' => $id))
                ->queryRow();

            return $region;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>