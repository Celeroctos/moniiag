<?php
class Doctor extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.doctors';
    }


    public function getAll() {
        try {
            $connection = Yii::app()->db;
            $doctors = $connection->createCommand()
                ->select('d.*')
                ->from('mis.doctors d')
                ->queryAll();

            foreach($doctors as $key => &$doctor) {
                $doctor['fio'] = $doctor['first_name'].' '.$doctor['middle_name'].' '.$doctor['last_name'];
            }

            return $doctors;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>