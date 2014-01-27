<?php
class PatientPrivilegie extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.privileges_per_patient';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $privilege = $connection->createCommand()
                ->select('p.*')
                ->from('mis.'.$this->tableName().' ppp')
                ->where('ppp.id = :id', array(':id' => $id))
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
            ->from('mis.'.$this->tableName().' ppp');

        if($filters !== false) {
            $this->getSearchConditions($privileges, $filters, array(

            ), array(
                'ppp' => array('id')
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