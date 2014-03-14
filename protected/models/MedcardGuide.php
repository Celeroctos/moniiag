<?php
class MedcardGuide extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcard_guides';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $guide = $connection->createCommand()
                ->select('mc.*')
                ->from('mis.medcard_guides mc')
                ->where('mc.id = :id', array(':id' => $id))
                ->queryRow();

            return $guide;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $guides = $connection->createCommand()
            ->select('mc.*')
            ->from('mis.medcard_guides mc');

        if($filters !== false) {
            $this->getSearchConditions($guides, $filters, array(

            ), array(
                'mc' => array('id', 'name'),
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false) {
            $guides->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $guides->limit($limit, $start);
        }

        return $guides->queryAll();
    }
}

?>