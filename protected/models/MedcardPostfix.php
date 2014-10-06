<?php
class MedcardPostfix extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcards_postfixes';
    }
	
	public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $postfixes = $connection->createCommand()
            ->select('mp.*')
            ->from(MedcardPostfix::tableName().' mp');

        if($filters !== false) {
            $this->getSearchConditions($postfixes, $filters, array(
            ), array(
                'mp' => array('id', 'value')
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false) {
            $postfixes->order($sidx.' '.$sord);
        }
		if($start !== false && $limit !== false) {
			$postfixes->limit($limit, $start);
		}

        return $postfixes->queryAll();

    }
}

?>