<?php
class Cabinet extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.cabinets';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $cabinet = $connection->createCommand()
                ->select('c.*')
                ->from('mis.cabinets c')
                ->where('c.id = :id', array(':id' => $id))
                ->queryRow();

            return $cabinet;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $cabinets = $connection->createCommand()
            ->select('c.*, w.name as ward, e.shortname as enterprise')
            ->from('mis.cabinets c')
            ->leftJoin('mis.enterprise_params e', 'c.enterprise_id = e.id')
            ->leftJoin('mis.wards w', 'c.ward_id = w.id');

        if($filters !== false) {
            $this->getSearchConditions($cabinets, $filters, array(

            ), array(
               'c' => array('id', 'cab_number', 'description'),
               'e' => array('enterprise'),
               'w' => array('ward')
            ), array(
               'enterprise' => 'shortname',
               'ward'  => 'name'
            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $cabinets->order($sidx.' '.$sord);
            $cabinets->limit($limit, $start);
        }

        return $cabinets->queryAll();
    }

    public function getByEnterprise($id) {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>