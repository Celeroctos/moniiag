<?php
class SheduleRest extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.shedule_rest';
    }

    public function getOne($id) {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($date, $doctorId) {
        $connection = Yii::app()->db;


    }

    public function getByEnterprise($id) {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>