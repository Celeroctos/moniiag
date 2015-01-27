<?php

class Patient extends MisActiveRecord {
    public function __construct($scenario = 'insert') {
        parent::__construct($scenario);
    }
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

	public function tableName() {
		return 'hospital.patient';
	}
	public function primaryKey() {
		return 'id';
	}
	public function attributeLabels() {
		return array(
		  'id' => 'ID'
		);
    }

    public function getPatient($formModel, $oms) {
        $result = $this->find('first_name = :first_name AND last_name = :last_name AND middle_name = :middle_name AND birthday = :birthday AND oms_id = :oms_id',
            array(
                ':first_name' => $oms->first_name,
                ':last_name' => $oms->last_name,
                ':middle_name' => $oms->middle_name,
                ':birthday' => $oms->birthday,
                ':oms_id' => $oms->id
            )
        );
        // Creates, if not exists
        if(!$result) {
            $result = $this->createPatient($formModel, $oms);
        }

        return $result;
    }

    public function createPatient($formModel, $oms) {
        $patient = new Patient();
        $patient->first_name = $oms->first_name;
        $patient->last_name = $oms->last_name;
        $patient->middle_name = $oms->middle_name;
        $patient->oms_id = $oms->id;
        $patient->birthday = $oms->birthday;
        if(!$patient->save()) {
            throw new Exception();
        }
var_dump($patient->primaryKey);
        exit();
        return $patient;
     }
}
?>