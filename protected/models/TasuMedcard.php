<?php
class TasuMedcard extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'dbo.t_book_65067';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = $this->getDbConnection();
        $medcards = $connection->createCommand()
            ->select('p.*')
            ->from(TasuMedcard::tableName().' tm')
            ->where('tm.version_end = :version_end', array(':version_end' => '9223372036854775807'));

        if($filters !== false) {
            $this->getSearchConditions($medcards, $filters, array(
            ), array(
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false ) {
            $medcards->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $medcards->limit($limit, $start);
        }

        return $medcards->queryAll();
    }
	
	public function getNumRows() {
        $connection = $this->getDbConnection();
        $numMedcards = $connection->createCommand()
            ->select('COUNT(*) as num')
            ->from(TasuMedcard::tableName().' tm')
            ->queryRow();
        return $numMedcards['num'];
    }
}

?>