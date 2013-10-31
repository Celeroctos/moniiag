<?php
class Medworker extends CActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medpersonal';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $medworker = $connection->createCommand()
                ->select('m.*')
                ->from('mis.medpersonal m')
                ->where('m.id = :id', array(':id' => $id))
                ->queryRow();

            return $medworker;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>