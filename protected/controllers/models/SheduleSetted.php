<?php
class SheduleSetted extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.doctor_shedule_setted';
    }

    public function getOne($id) {
        try {


        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows() {

    }

    public function getByEnterprise($id) {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // Получить всех id врачей, которые могут принмать по этой дате
    public function getAllPerDate($date) {
        $weekday = date('w', strtotime($date));
        $connection = Yii::app()->db;
        try {
            $doctors = $connection->createCommand()
                    ->selectDistinct('ss.employee_id')
                    ->from(SheduleSetted::tableName().' ss')
                    ->where('weekday = :weekday AND NOT EXISTS(SELECT ss2.* FROM '
                        .SheduleSetted::tableName()
                        .' ss2 WHERE weekday IS NULL AND day = :date)',
                    array(':weekday' => $weekday, ':date' => $date));

            return $doctors->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

}

?>