<?php

class Patient extends MisActiveRecord {

	public function tableName() {
		return 'hospital.patient';
	}
	public function primaryKey() {
		return 'id';
	}
	public function attributeLabels() {
		return array(
		  'id' => 'ID',
		  'first_name' => 'Имя',
		  'last_name' => 'Фамилия',
		  'middle_name' => 'Отчество',
		  'card_number' => 'Номер карты',
		  'fio' => 'ФИО',
		  'age' => 'Возраст',
		  'number_of_weeks' => 'Срок',
		  'hospitalization_date' => 'Дата госпитализации'
		);
    }
}
?>