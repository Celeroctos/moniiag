<?php
/**
 * СМОТРИ КЛАСС Medpersonal!!!
 */
class Medworker extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medpersonal';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $medworkers = $connection->createCommand()
            ->select('m.*, mt.name as medpersonal_type')
            ->from('mis.medpersonal m')
            ->join('mis.medpersonal_types mt', 'm.type = mt.id');

        if($filters !== false) {
            $this->getSearchConditions($medworkers, $filters, array(
            ), array(
                'm' => array('id', 'name'),
                'mt' => array('medpersonal_type')
            ), array(
                'medpersonal_type' => 'name'
            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $medworkers->order($sidx.' '.$sord);
            $medworkers->limit($limit, $start);
        }

        return $medworkers->queryAll();

    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $medworker = $connection->createCommand()
                ->select('m.*')
                ->from('mis.medpersonal m')
                ->where('m.id = :id', array(':id' => $id))
                ->queryRow();

            return $medworker;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>