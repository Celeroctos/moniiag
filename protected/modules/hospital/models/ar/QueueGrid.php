<?php
class QueueGrid extends MisActiveRecord {
	public $defaultPageSize = 10;

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
            'id' => 'ID'
        );
    }

    public function getConnection() {
        return Yii::app()->db;
    }
}
?>