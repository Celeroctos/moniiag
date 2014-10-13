<?php
class Degree extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.degrees';
    }

    public function primaryKey()
    {
        return 'id';
        // Для составного первичного ключа следует использовать массив:
        // return array('pk1', 'pk2');
    }
}

?>