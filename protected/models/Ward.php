<?php
class Ward extends CActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.wards';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $ward = $connection->createCommand()
                ->select('w.*')
                ->from('mis.wards w')
                ->where('w.id = :id', array(':id' => $id))
                ->queryRow();

            return $ward;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>