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
				->from(TasuEmployee::model()->tableName().' te');
			$row = $max->queryRow();
			return $row['num'];
        } catch(Exception $e) {
            echo $e->getMessage();
        }
	}
	
	public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = $this->getDbConnection();
        $employees = $connection->createCommand()
            ->select('te.*')
            ->from(TasuEmployee::model()->tableName().' te')
            ->where('te.version_end = :version_end', array(':version_end' => '9223372036854775807'));

        if($filters !== false) {
            $this->getSearchConditions($employees, $filters, array(
            ), array(
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false ) {
            $employees->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $employees->limit($limit, $start);
        }

        return $employees->queryAll();
    }
	
	public function getNumRows() {
        $connection = $this->getDbConnection();
        $numEmployees = $connection->createCommand()
            ->select('COUNT(*) as num')
            ->from(TasuEmployee::model()->tableName().' te')
            ->queryRow();
        return $numEmployees['num'];
    }
}
?>