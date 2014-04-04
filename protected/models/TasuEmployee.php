<?php
class TasuEmployee extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'dbo.t_employee_22089';
    }
	
	public function getLastUID() {
		try {
			$connection = TasuEmployee::getDbConnection();
			$max = $connection->createCommand()
				->select('MAX(te.uid) as num')
				->from(TasuEmployee::tableName().' te');
			$row = $max->queryRow();
			return $row['num'];
        } catch(Exception $e) {
            echo $e->getMessage();
        }
	}
}
?>