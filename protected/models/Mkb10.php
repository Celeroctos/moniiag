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

    public function getRowsByLevel($onlylikes, $parentId = 0, $sidx = false, $sord = false, $start = false, $limit = false, $higherWhenLevel = false) {
        $connection = Yii::app()->db;

        $mkb10 = $connection->createCommand()
            ->select('m.*')
            ->from('mis.mkb10 m');

        if($onlylikes) {
            $mkb10->join('mis.mkb10_likes ml', 'm.id = ml.mkb10_id');
            $mkb10->where('ml.medworker_id = :medworker_id', array(':medworker_id' => Yii::app()->user->medworkerId));
        }

        if($higherWhenLevel !== false) {
            $mkb10->andWhere('m.level > :level', array(':level' => $higherWhenLevel));
        }

        // Если не задан уровень, вынимаем все записи
        if($parentId !== false) {
            $mkb10->andWhere('m.parent_id = :parent_id', array(':parent_id' => $parentId));
        }

        if($sidx !== false && $sord !== false) {
            $mkb10->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $mkb10->limit($limit, $start);
        }

        $result = $mkb10->queryAll();
        return $result;
    }

	public function getRows($onlylikes, $filters, $medworkerId, $sidx = false, $sord = false, $start = false, $limit = false, $higherWhenLevel = false, $operator = 'gt') {
        $connection = Yii::app()->db;
		$mkb10 = $connection->createCommand()
            ->selectDistinct('m.*')
            ->from('mis.mkb10 m');

        if($onlylikes) {
            if($medworkerId !== false) {
                $mkb10->join('mis.mkb10_likes ml', 'm.id = ml.mkb10_id');
                $mkb10->where('ml.medworker_id = :medworker_id', array(':medworker_id' => $medworkerId));
            } else {
                $mkb10->join('mis.mkb10_distrib md', 'm.id = md.mkb10_id');
            }
        }

        if($filters !== false) {
            $this->getSearchConditions($mkb10, $filters, array(

            ), array(
                'm' => array('id', 'description')
            ), array(
                
            ));
        }

        if($higherWhenLevel !== false) {
            if($operator == 'gt') {
                $mkb10->andWhere("m.level > :level
                                 AND substring(m.description from 4 for 1) != '-'",
                                array(':level' => $higherWhenLevel));
            }
            if($operator == 'eq') {
                $mkb10->andWhere('m.level = :level', array(':level' => $higherWhenLevel));
            }
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $mkb10->order($sidx.' '.$sord);
            $mkb10->limit($limit, $start);
        }

        return $mkb10->queryAll();
    }
	
    public function getNumRows($onlylikes = false, $medworkerId) {
        $connection = Yii::app()->db;

        $mkb10 = $connection->createCommand()
            ->select('count(m.*) as num')
            ->from('mis.mkb10 m');

        if($onlylikes) {
            if($medworkerId !== false) {
                $mkb10->join('mis.mkb10_likes ml', 'm.id = ml.mkb10_id');
                $mkb10->where('ml.medworker_id = :medworker_id', array(':medworker_id' => $medworkerId));
            } else {
                $mkb10->join('mis.mkb10_distrib md', 'm.id = md.mkb10_id');
            }
        }

        $result = $mkb10->queryRow();
        return $result['num'];
    }
}

?>