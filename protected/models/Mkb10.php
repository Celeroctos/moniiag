<?php
class Mkb10 extends MisActiveRecord {
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

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $mkb10->order($sidx.' '.$sord);
            $mkb10->limit($limit, $start);
        }

        $result = $mkb10->queryAll();
        return $result;
    }

	public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
		$mkb10 = $connection->createCommand()
            ->select('m.*')
            ->from('mis.mkb10 m');

        if($filters !== false) {
            $this->getSearchConditions($mkb10, $filters, array(

            ), array(
                'm' => array('id', 'description')
            ), array(
                
            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $mkb10->order($sidx.' '.$sord);
            $mkb10->limit($limit, $start);
        }

        return $mkb10->queryAll();
    }
	
    public function getNumRows() {
        $connection = Yii::app()->db;

        $mkb10 = $connection->createCommand()
            ->select('count(m.*) as num')
            ->from('mis.mkb10 m');

        $result = $mkb10->queryRow();
        return $result['num'];
    }
}

?>