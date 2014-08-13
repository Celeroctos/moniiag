<?php
class TasuFakeGreetingsBufferSecDiag extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.tasu_fake_greetings_secondary_diag';
    }
}

?>