<?php
class MedcardSeparator extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcards_separators';
    }
	
	public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $separators = $connection->createCommand()
            ->select('ms.*')
            ->from(MedcardSeparator::tableName().' ms');

        if($filters !== false) {
            $this->getSearchConditions($separators, $filters, array(
            ), array(
                'ms' => array('id', 'value')
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false) {
            $separators->order($sidx.' '.$sord);
        }
		if($start !== false && $limit !== false) {
			$separators->limit($limit, $start);
		}

        return $separators->queryAll();
    }
	
	public function findAllNotUsed() {
		$connection = Yii::app()->db;
        $separators = $connection->createCommand()
            ->select('ms.*')
            ->from(MedcardSeparator::tableName().' ms')
			->where('NOT EXISTS(SELECT * 
								FROM '.MedcardRule::model()->tableName().' mr 
								WHERE mr.postfix_separator_id = ms.id OR mr.prefix_separator_id = ms.id)');
        return $separators->queryAll();
	}
}

?>