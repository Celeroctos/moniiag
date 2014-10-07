<?php
class TasuCladrStreet extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'dbo.t_street_48326';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = $this->getDbConnection();
        $sql = "DECLARE @uid integer;

		SET ROWCOUNT ".$start.";

		SELECT @uid = p.uid
		  FROM PDPStdStorage.".TasuCladrStreet::tableName()." p
		  ORDER BY p.uid ASC;

		SET ROWCOUNT ".$limit.";

		SELECT p.*
		  FROM PDPStdStorage.".TasuCladrStreet::tableName()." p
		  WHERE p.uid >= @uid
		  ORDER BY p.uid ASC;

		SET ROWCOUNT 0;";

        return $connection->createCommand($sql)->queryAll();
    }

    public function getNumRows() {
        $connection = $this->getDbConnection();
        $numStreets = $connection->createCommand()
            ->select('COUNT(*) as num')
            ->from(TasuCladrStreet::tableName().' tcst')
            ->queryRow();
        return $numStreets['num'];
    }
}

?>