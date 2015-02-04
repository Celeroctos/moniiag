<?php
class TasuGreetingsBufferHistory extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.tasu_greetings_buffer_history';
    }

    public function primaryKey() {
        return 'id';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        try {
            $connection = Yii::app()->db;
            $bufferHistory = $connection->createCommand()
                ->select('tgbh.*')
                ->from(TasuGreetingsBufferHistory::model()->tableName().' tgbh');

            if($filters !== false) {
                $this->getSearchConditions($bufferHistory, $filters, array(
                ), array(
                ), array(
                ));
            }

            if($sidx !== false && $sord !== false) {
                $bufferHistory->order($sidx.' '.$sord);
            }
            if($start !== false && $limit !== false) {
                $bufferHistory->limit($limit, $start);
            }

            return $bufferHistory->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>