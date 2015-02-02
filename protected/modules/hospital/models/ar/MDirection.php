<?php

class MDirection extends MisActiveRecord {

    public $doctor_id;
    public $patient_id;
    public $type;
    public $is_pregnant;
    public $ward_id;
    public $create_date;
    public $id;
    public $pregnant_term;

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

	public function tableName() {
		return 'hospital.medical_directions';
	}
	public function primaryKey() {
		return 'id';
	}
	public function attributeLabels() {
		return array(
		  'id' => 'ID'
		);
    }

    public function getConnection() {
        return Yii::app()->db;
    }

    public function create($patient, $formModel) {
        $mDirection = new MDirection();
        $mDirection->doctor_id = $formModel->doctorId;
        $mDirection->patient_id = $patient->id;
        $mDirection->type = $formModel->type;
        $mDirection->is_pregnant = $formModel->isPregnant;
        $mDirection->ward_id = $formModel->wardId;
        $mDirection->create_date = date('Y-m-d');
        $mDirection->pregnant_term = $formModel->pregnantTerm;

        if(!$mDirection->save()) {
            throw new Exception('Невозможно сохранить направление для пациента '.$patient->id);
        }

        return $mDirection;
    }

    public function findAllPerPatientId($patientId) {
        try {
            $connection = $this->getConnection();
            $directions = $connection->createCommand()
                    ->select('md.*, p.*, w.*, ep.*')
                    ->from($this->tableName().' md')
                    ->join(Patient::model()->tableName().' p', 'md.patient_id = p.id')
                    ->join(Ward::model()->tableName().' w', 'w.id = md.ward_id')
                    ->leftJoin(Enterprise::model()->tableName().' ep', 'ep.id = w.enterprise_id')
                    ->where('p.id = :patient_id', array(':patient_id' => $patientId));
            return $directions->queryAll();
        } catch(Exception $e) {
            throw new Exception($e);
        }
    }
}
?>