<?php
class MedcardElementDependence extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcard_elements_dependences';
    }

    public function getOne($id) {
        try {


        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getTypesList() {
        return $this->typesList;
    }

    public function getRows($id = false) {
        $connection = Yii::app()->db;
        $dependences = $connection->createCommand()
            ->select('med.*, me.label as element, mgv.value as value, me2.label as dep_element, me.label_display as me_display_label, me2.label_display as me2_display_label,')
            ->from('mis.medcard_elements_dependences med')
            ->join('mis.medcard_elements me', 'me.id = med.element_id')
            ->join('mis.medcard_elements me2', 'me2.id = med.dep_element_id')
            ->join('mis.medcard_guide_values mgv', 'mgv.id = med.value_id');

        if($id != false) {
            $dependences->where('med.element_id = :element_id', array(':element_id' => $id));
        }

        return $dependences->queryAll();
    }
}

?>