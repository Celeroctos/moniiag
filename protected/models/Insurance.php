<?php
class Insurance extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.insurances';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $insurances = $connection->createCommand()
            ->select('ins.*')
            ->from('mis.insurances ins');

        if($filters !== false) {
            $this->getSearchConditions($insurances, $filters, array(
            ), array(
                'ins' => array('id', 'name')
            ), array(

            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $insurances->order($sidx.' '.$sord);
            $insurances->limit($limit, $start);
        }

        return $insurances->queryAll();

    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $insurance = $connection->createCommand()
                ->select('ins.*')
                ->from('mis.insurances ins')
                ->where('ins.id = :id', array(':id' => $id))
                ->queryRow();

            return $insurance ;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>