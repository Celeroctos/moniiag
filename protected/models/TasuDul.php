<?php
class TasuDul extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'dbo.t_dul_44571';
    }

	public function getLastUID() {
		try {
			$connection = TasuDul::getDbConnection();
			$max = $connection->createCommand()
				->select('MAX(tt.uid) as num')
				->from(TasuDul::model()->tableName().' tt');
			$row = $max->queryRow();
			return $row['num'];
        } catch(Exception $e) {
            echo $e->getMessage();
        }
	}
}

?>