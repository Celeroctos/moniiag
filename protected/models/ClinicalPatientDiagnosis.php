<?php
class ClinicalPatientDiagnosis extends MisActiveRecord {
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'mis.clinical_diagnosis_per_patient';
	}

	public function getOne($id) {
		try {


		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}


	public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {

	}
	
	public function findDiagnosis($greetingId, $type) {
		try {
			$connection = Yii::app()->db;
			$diagnosis = $connection->createCommand()
				->select('cdpp.*, cd.*')
				->from(ClinicalPatientDiagnosis::tableName().' cdpp')
				->join(ClinicalDiagnosis::tableName().' cd', 'cdpp.diagnosis_id = cd.id')
				->where('cdpp.greeting_id = :greeting_id AND cdpp.type = :type', array(':greeting_id' => $greetingId, ':type' => $type));

			return $diagnosis->queryAll();

		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

}

?>