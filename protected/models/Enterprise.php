<?php
class Enterprise extends CActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.enterprise_params';
    }


    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $enterprise = $connection->createCommand()
                ->select('ep.*')
                ->from('mis.enterprise_params ep')
                ->where('ep.id = :id', array(':id' => $id))
                ->queryRow();

            return $enterprise;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>