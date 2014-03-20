<?php
class TasuTap extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db2;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'PDPStdStorage.dbo.p_tapset_07615';
    }


}

?>