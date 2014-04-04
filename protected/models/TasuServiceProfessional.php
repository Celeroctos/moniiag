<?php
class TasuServiceProfessional extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'dbo.t_serviceprofessional_43322';
    }
	
	public function getLastUID() {
		try {
			$connection = TasuServiceProfessional::getDbConnection();
			$max = $connection->createCommand()
				->select('MAX(ts.uid) as num')
				->from(TasuServiceProfessional::tableName().' ts');
			$row = $max->queryRow();
			return $row['num'];
        } catch(Exception $e) {
            echo $e->getMessage();
        }
	}
}
?>