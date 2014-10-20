<?php
class QuickPanelIcon extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.quick_panel';
    }

    public function getOne($id) {
        try {


        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($userId = false) {
        if(!$userId) {
            $userId = Yii::app()->user->id;
        }

        $connection = Yii::app()->db;
        $icons = $connection->createCommand()
            ->select('qp.*')
            ->from(QuickPanelIcon::model()->tableName().' qp')
            ->where('qp.user_id = :user_id', array(':user_id' => $userId));

        return $icons->queryAll();
    }
}

?>