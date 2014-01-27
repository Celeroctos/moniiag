<?php
class Privilege extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.privileges';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $privilege = $connection->createCommand()
                ->select('p.*')
                ->from('mis.privileges p')
                ->where('p.id = :id', array(':id' => $id))
                ->queryRow();

            return $privilege;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $privileges = $connection->createCommand()
            ->select('p.*')
            ->from('mis.privileges p');

        if($filters !== false) {
            $this->getSearchConditions($privileges, $filters, array(

            ), array(
                'p' => array('id', 'name', 'code')
            ), array(

            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $privileges->order($sidx.' '.$sord);
            $privileges->limit($limit, $start);
        }

        return $privileges->queryAll();
    }
}

?>