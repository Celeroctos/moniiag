<?php
class Shift extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.shifts';
    }


    public function getAll() {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $shifts = $connection->createCommand()
            ->select('s.*')
            ->from('mis.shifts s');

        if($filters !== false) {
            $this->getSearchConditions($shifts, $filters, array(
            ), array(
                's' => array('module_id', 'id', 'name', 'value')
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false) {
            $shifts->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $shifts->limit($limit, $start);
        }

        return $shifts->queryAll();
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $shift = $connection->createCommand()
                ->select('r.*')
                ->from('mis.shifts r')
                ->where('r.id = :id', array(':id' => $id))
                ->queryRow();

            return $shift;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>