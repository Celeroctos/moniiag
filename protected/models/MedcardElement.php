<?php
class MedcardElement extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcard_elements';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $element = $connection->createCommand()
                ->select('mc.*')
                ->from('mis.medcard_elements mc')
                ->where('mc.id = :id', array(':id' => $id))
                ->queryRow();

            return $element;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $elements = $connection->createCommand()
            ->select('me.*, mg.name as guide, mc.name as categorie')
            ->from('mis.medcard_elements me')
            ->leftJoin('mis.medcard_guides mg', 'mg.id = me.guide_id')
            ->leftJoin('mis.medcard_categories mc', 'mc.id = me.categorie_id');

        if($filters !== false) {
            $this->getSearchConditions($elements, $filters, array(

            ), array(
                'me' => array('id', 'label'),
                'mg' => array('categorie'),
                'mc' => array('guide')
            ), array(
                'categorie' => 'name',
                'guide' => 'name'
            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $elements->order($sidx.' '.$sord);
            $elements->limit($limit, $start);
        }

        return $elements->queryAll();
    }
}

?>