<?php
class CancelledGreeting extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.cancelled_greetings';
    }
}

?>