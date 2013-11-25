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


    public function getAll($forPregnant = false) {
        try {
            $connection = Yii::app()->db;
            $doctors = $connection->createCommand()
                ->select('d.*')
                ->from('mis.doctors d');

            if($forPregnant !== false) {
                $doctors->join('mis.medpersonal m', 'd.post_id = m.id')
                    ->where('m.is_for_pregnants = 1');
            }
            $doctors = $doctors->queryAll();

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