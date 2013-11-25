<?php
class Pregnant extends CActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.pregnants';
    }

    public function getRows() {

    }
}

?>