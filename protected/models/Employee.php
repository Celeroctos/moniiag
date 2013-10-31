<?php
class Employee extends CActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.doctors';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $employee = $connection->createCommand()
                ->select('m.*')
                ->from('mis.doctors m')
                ->where('m.id = :id', array(':id' => $id))
                ->queryRow();

            return $employee;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>