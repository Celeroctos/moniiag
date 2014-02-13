<?php
class DoctorCabinet extends MisActiveRecord  {

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.doctor_cabinet';
    }

    /*public function getOne($id) {
        
        try {
            $connection = Yii::app()->db;
            $categorie = $connection->createCommand()
                ->select('dc.*')
                ->from('mis.doctor-cabinet mc')
                ->where('mc.id = :id', array(':id' => $id))
                ->queryRow();

            return $categorie;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }*/
    
    public function getRows($filters) {

    }
    
    // Получить по doctor_id -> cabinet_id
    //   (В каком кабинете работает врач)
    public function getCabinetByDoctor($doctorId)
    {
        
    }
    
}
?>