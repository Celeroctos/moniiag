<?php
class TasuPatient extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'dbo.t_patient_10905';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = $this->getDbConnection();
        $patients = $connection->createCommand()
            ->select('p.*')
            ->from(TasuPatient::tableName().' p')
            ->where('p.version_end = :version_end', array(':version_end' => '9223372036854775807'));

        if($filters !== false) {
            $this->getSearchConditions($patients, $filters, array(
            ), array(
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false ) {
            $patients->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $patients->limit($limit, $start);
        }

        return $patients->queryAll();
    }
	
	public function getNumRows() {
        $connection = $this->getDbConnection();
        $numPatients = $connection->createCommand()
            ->select('COUNT(*) as num')
            ->from(TasuPatient::tableName().' tp')
            ->queryRow();
        return $numPatients['num'];
    }
}

?>