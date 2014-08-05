<?php
class TasuInsurance extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'dbo.t_smo_30821';
    }
	
	public function getLastUID() {
		try {
			$connection = TasuInsurance::getDbConnection();
			$max = $connection->createCommand()
				->select('MAX(s.uid) as num')
				->from(TasuInsurance::tableName().' s')
				->where('s.version_end = :version_end', array(':version_end' => '9223372036854775807'));
			$row = $max->queryRow();
			return $row['num'];
        } catch(Exception $e) {
            echo $e->getMessage();
        }
	}
	
	public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = $this->getDbConnection();
        $insurances = $connection->createCommand()
            ->select('s.*')
            ->from(TasuInsurance::tableName().' s')
            ->where('s.version_end = :version_end', array(':version_end' => '9223372036854775807'));

        if($filters !== false) {
            $this->getSearchConditions($insurances, $filters, array(
            ), array(
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false ) {
            $insurances->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $insurances->limit($limit, $start);
        }

        return $insurances->queryAll();
    }
	
	public function getNumRows() {
        $connection = $this->getDbConnection();
        $numInsurances = $connection->createCommand()
            ->select('COUNT(*) as num')
            ->from(TasuInsurance::tableName().' s')
			->where('s.version_end = :version_end', array(':version_end' => '9223372036854775807'))
            ->queryRow();
			
        return $numInsurances['num'];
    }
}
?>