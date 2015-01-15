<?php
class TasuTap extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'PDPStdStorage.dbo.t_tap_10874';
    }

	public function getLastUID() {
		try {
			$connection = TasuTap::getDbConnection();
			$max = $connection->createCommand()
				->select('MAX(tt.uid) as num')
				->from(TasuTap::model()->tableName().' tt');
			$row = $max->queryRow();
			return $row['num'];
        } catch(Exception $e) {
            echo $e->getMessage();
        }
	}
}

?>