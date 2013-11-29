<?php
class DoctorCabinet extends MisActiveRecord  {

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.doctor-cabinet';
    }

    public function getRows($filters) {

    }
}
?>