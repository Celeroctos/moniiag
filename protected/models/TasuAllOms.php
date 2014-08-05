<?php
class TasuAllOms extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db3;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ut.ut_PolReg_UsReg';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = $this->getDbConnection();
		$sql = "DECLARE @env char(50);
 
		SET ROWCOUNT ".$start.";

		SELECT @env = O.ENP
		  FROM PDPRegStorage.ut.ut_PolReg_UsReg O
		  ORDER BY O.ENP ASC;
		 
		SET ROWCOUNT ".$limit.";

		SELECT O.*
		  FROM PDPRegStorage.ut.ut_PolReg_UsReg O 
		  WHERE O.ENP >= @env
		  ORDER BY O.ENP ASC;

		SET ROWCOUNT 0;";
        return $connection->createCommand($sql)->queryAll();
    }
	
	public function getNumRows() {
        $connection = $this->getDbConnection();
        $numoms = $connection->createCommand()
            ->select('COUNT(*) as num')
            ->from(TasuAllOms::tableName().' upu')
			->where("REPLACE([upu].[ENP], CHAR(32), '') != ''")
            ->queryRow();
        return $numoms['num'];
    }
}

?>