<?php
class Post extends CActiveRecord {
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
        $oms = $connection->createCommand()
            ->select('p.*')
            ->from('mis.medpersonal p');

        if($filters !== false) {
            $this->getSearchConditions($oms, $filters, array(
            ), array(
                'p' => array('id', 'name')
            ), array(
            ));
        }
        return $oms->queryAll();
    }
}

?>