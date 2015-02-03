<?php
class ComissionGrid extends MisActiveRecord {
	public $defaultPageSize = 10;

    public $fio;
    public $patient_id;
    public $birthday;
    public $ward_name;
    public $doctor_id;
    public $is_pregnant;
    public $pregnant_term;
    public $type;
    public $create_date;
    public $direction_id;
    public $comission_type_desc;
    public $age;
    public $hospitalization_date;
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
            'ward_name' => 'Отделение',
            'pregnant_term' => 'Срок',
            'comission_type_desc' => 'Тип записи',
            'hospitalization_date' => 'Дата госпитализации'
        );
    }

    // This changes model after finding
    public function afterFind() {
        // Age
        $datetime = new DateTime($this->birthday);
        $interval = $datetime->diff(new DateTime(date("Y-m-d")));
        $this->age = $interval->format("%Y").' лет';

        // Icon, if hospitalization date is not accepted
        if(!$this->hospitalization_date) {
            $this->hospitalization_date = '<a href="#" id="hd'.$this->direction_id.'" class="changeHospitalizationDate"><img src="'.Yii::app()->request->baseUrl.'/images/icons/evolution-calendar.png" width="24" height="24" alt="Определить дату" title="Определить дату" ></a>';
        }
    }

    public function getConnection() {
        return Yii::app()->db;
    }
}
?>