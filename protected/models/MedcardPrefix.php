<?php
class MedcardPrefix extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcards_prefixes';
    }
	
	public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $prefixes = $connection->createCommand()
            ->select('mp.*')
            ->from(MedcardPrefix::tableName().' mp');

        if($filters !== false) {
            $this->getSearchConditions($prefixes, $filters, array(
            ), array(
                'mp' => array('id', 'value')
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false) {
            $prefixes->order($sidx.' '.$sord);
        }
		if($start !== false && $limit !== false) {
			$prefixes->limit($limit, $start);
		}

        return $prefixes->queryAll();

    }
}

?>