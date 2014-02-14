<?php
class DoctorCabinet extends MisActiveRecord  {

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
<<<<<<< HEAD
        return 'mis.doctor_cabinet';
=======
        return 'mis.doctor-cabinet';
>>>>>>> ebaa99cc87508d7084883441d9f0f3720e34fd13
    }

    public function getRows($filters) {

    }
}
?>