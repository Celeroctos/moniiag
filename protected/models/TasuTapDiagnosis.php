<?php
class TasuTapDiagnosis extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'PDPStdStorage.dbo.t_tapdiagnosis_31571';
    }

	public function getLastUID() {
		try {
			$connection = TasuTap::getDbConnection();
			$max = $connection->createCommand()
				->select('MAX(ttd.uid) as num')
				->from(TasuTapDiagnosis::model()->tableName().' ttd');
			$row = $max->queryRow();
			return $row['num'];
        } catch(Exception $e) {
            echo $e->getMessage();
        }
	}
}

?>