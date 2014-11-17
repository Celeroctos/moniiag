<?php
class Log extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.logs';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $log = $connection->createCommand()
                ->select('l.*')
                ->from(Log::model()->tableName().' l')
                ->where('m.id = :id', array(':id' => $id))
                ->queryRow();

            return $log;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $logs = $connection->createCommand()
            ->select('l.*, u.login')
			->from(Log::model()->tableName().' l')
			->leftJoin(User::model()->tableName().' u', 'u.id = l.user_id');

        if($filters !== false) {
            $this->getSearchConditions($logs, $filters, array(

            ), array(
                'l' => array('id', 'user_id', 'changedate')
            ), array(

            ));
        }

        if($sidx !== false && $sord !== false ) {
            $logs->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $logs->limit($limit, $start);
        }

        return $logs->queryAll();
    }
	
	public function getNumRows($filters) {
		$connection = Yii::app()->db;
        $logs = $connection->createCommand()
            ->select('COUNT(l.*) as num')
            ->from(Log::model()->tableName().' l');
			
		if($filters !== false) {
            $this->getSearchConditions($logs, $filters, array(

            ), array(
                'l' => array('id', 'user_id', 'changedate')
            ), array(

            ));
        }
		
		return $logs->queryRow();
	}
}

?>