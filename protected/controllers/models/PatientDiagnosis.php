<?php
class PatientDiagnosis extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.diagnosis_per_patient';
    }

    public function getOne($id) {
        try {


        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {

    }

    public function findDiagnosis($greetingId, $type) {
        try {
            var_dump($greetingId);
            var_dump($type);
            exit();

            $connection = Yii::app()->db;
            $diagnosis = $connection->createCommand()
                ->select('dpp.*, m.*')
                ->from(PatientDiagnosis::tableName().' dpp')
                ->join(Mkb10::tableName().' m', 'dpp.mkb10_id = m.id')
                ->where('dpp.greeting_id = :greeting_id AND dpp.type = :type', array(':greeting_id' => $greetingId, ':type' => $type));

            return $diagnosis->queryAll();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

}

?>