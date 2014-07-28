<?php
class InsuranceRegion extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.insurances_regions';
    }

    public static function findRegions($insuranceId)
    {
        try {
           // var_dump($insuranceId);
           // exit();
            $connection = Yii::app()->db;
            $regions = $connection->createCommand()
                ->select('ir.*, r.*')
                ->from(InsuranceRegion::tableName().' ir')
                ->join(CladrRegion::tableName().' r', 'ir.region_id = r.id')
                ->where('ir.insurance_id = :ins', array(':ins' => $insuranceId));

            return $regions->queryAll();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>