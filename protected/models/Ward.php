<?php
class Ward extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.wards';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $ward = $connection->createCommand()
                ->select('w.*, mr.id as rule_id')
                ->from('mis.wards w')
				->leftJoin(MedcardRule::model()->tableName().' mr', 'mr.id = w.rule_id')
                ->where('w.id = :id', array(':id' => $id))
                ->queryRow();

            return $ward;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $wards = $connection->createCommand()
            ->select('mw.*, e.shortname as enterprise_name, mr.name as rule, mr.id as rule_id')
            ->from('mis.wards mw')
            ->join('mis.enterprise_params e', 'mw.enterprise_id = e.id')
			->leftJoin(MedcardRule::model()->tableName().' mr', 'mr.id = mw.rule_id');

        if($filters !== false) {
            $this->getSearchConditions($wards, $filters, array(

            ), array(
                'mw' => array('id', 'name', 'enterprise_id'),
                'e' => array('enterprise_name'),
				'mr' => array('rule_id', 'rule')
            ), array(
                'enterprise_name' => 'shortname',
				'rule' => 'name',
				'rule_id' => 'id'
            ));
        }

        if($sidx !== false && $sord !== false ) {
            $wards->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $wards->limit($limit, $start);
        }

        return $wards->queryAll();
    }

    public function getByEnterprise($id) {
        try {
            $connection = Yii::app()->db;
            $wards = $connection->createCommand()
                ->select('w.*')
                ->from('mis.wards w')
                ->where('w.enterprise_id = :id', array(':id' => $id))
                ->order('w.name asc')
                ->queryAll();

            return $wards;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getAll() {
        try {
            $connection = Yii::app()->db;
            $wards = $connection->createCommand()
                ->select('w.*, e.shortname as enterprise_name')
                ->from('mis.wards w')
                ->leftJoin(Enterprise::model()->tableName().' e', 'e.id = w.enterprise_id')
                ->order('w.name asc')
                ->queryAll();

            return $wards;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>