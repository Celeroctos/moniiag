<?php
class MedcardGuideValue extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcard_guide_values';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $guideValue = $connection->createCommand()
                ->select('mgv.*')
                ->from('mis.medcard_guide_values mgv')
                ->where('mgv.id = :id', array(':id' => $id))
                ->queryRow();

            return $guideValue;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $guideId, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $guideValues = $connection->createCommand()
            ->select('mgv.*')
            ->from('mis.medcard_guide_values mgv')
            ->where('mgv.guide_id = :guide_id', array(':guide_id' => $guideId));

        if($filters !== false) {
            $this->getSearchConditions($guideValues, $filters, array(

            ), array(
                'mgv' => array('id', 'value'),
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $guideValues->order($sidx.' '.$sord);
            $guideValues->limit($limit, $start);
        }

        return $guideValues->queryAll();
    }
}

?>