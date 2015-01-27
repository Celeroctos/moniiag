<?php
class ComissionGrid extends MisActiveRecord {
	public $defaultPageSize = 10;

    public $fio;
    public $patient_id;
    public $birthday;
    public $ward_name;
    public $doctor_id;
    public $is_pregnant;
    public $type;
    public $create_date;
    public $direction_id;
    public $id;

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'hospital.comission_grid';
    }

    public function primaryKey() {
        return 'id';
    }

    public function attributeLabels() {
        return array(
            'direction_id' => 'ID',
            'fio' => 'ФИО',
            'birthday' => 'День рождения',
            'ward_name' => 'Отделение'
        );
    }

    public function getConnection() {
        return Yii::app()->db;
    }
}
?>