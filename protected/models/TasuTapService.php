<?php
class TasuTapService extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'PDPStdStorage.dbo.t_tapservice_61560';
    }

	public function getLastUID() {
		try {
			$connection = TasuTapService::getDbConnection();
			$max = $connection->createCommand()
				->select('MAX(tts.uid) as num')
				->from(TasuTapService::model()->tableName().' tts');
			$row = $max->queryRow();
			return $row['num'];
        } catch(Exception $e) {
            echo $e->getMessage();
        }
	}
}

?>