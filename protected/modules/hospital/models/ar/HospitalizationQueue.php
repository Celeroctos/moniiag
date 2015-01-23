<?php

class HospitalizationQueue extends MisActiveRecord {

	public function tableName() {
		return 'hospital.hospitalization_queue';
	}
	public function primaryKey() {
		return 'id';
	}
	public function attributeLabels() {
		return array(
		  'id' => 'ID',
		  'type' => 'Тип очереди',
		  'num_pre' => 'Кол-во предв. запись',
		  'num_queue' => 'Кол-во живая очередь',
		  'comission_date' => 'День комиссии'
        );
    }
}
?>