<?php
class Mkb10 extends CActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.mkb10';
    }

    public function getRowsByLevel($parentId = 0, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;

        $mkb10 = $connection->createCommand()
            ->select('m.*')
            ->from('mis.mkb10 m');

        // Если не задан уровень, вынимаем все записи
        if($parentId !== false) {
            $mkb10->where('m.parent_id = :parent_id', array(':parent_id' => $parentId));
        }

        if($sidx && $sord && $start && $limit) {
            $mkb10->order($sidx.' '.$sord);
            $mkb10->limit($limit, $start);
        }

        $result = $mkb10->queryAll();
        return $result;
    }
}

?>