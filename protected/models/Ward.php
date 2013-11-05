<?php
class Ward extends CActiveRecord {
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
                ->select('w.*')
                ->from('mis.wards w')
                ->where('w.id = :id', array(':id' => $id))
                ->queryRow();

            return $ward;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $wards = $connection->createCommand()
            ->select('mw.*, e.shortname as enterprise_name')
            ->from('mis.wards mw')
            ->join('mis.enterprise_params e', 'mw.enterprise_id = e.id');


        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $wards->order($sidx.' '.$sord);
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
                ->queryAll();

            return $wards;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>