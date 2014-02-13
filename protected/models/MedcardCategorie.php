<?php
class MedcardCategorie extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcard_categories';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $categorie = $connection->createCommand()
                ->select('mc.*')
                ->from('mis.medcard_categories mc')
                ->where('mc.id = :id', array(':id' => $id))
                ->queryRow();

            return $categorie;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $categories = $connection->createCommand()
            ->select('mc.*, mc2.name as parent')
            ->from('mis.medcard_categories mc')
			->leftJoin('mis.medcard_categories mc2', 'mc.parent_id = mc2.id');

        if($filters !== false) {
            $this->getSearchConditions($categories, $filters, array(

            ), array(
                'mc' => array('id', 'name'),
            ), array(
            ));
        }

        if($start !== false && $limit !== false) {
            $categories->limit($limit, $start);
        }

        if($sidx !== false && $sord !== false) {
            $categories->order($sidx.' '.$sord);
        }

        return $categories->queryAll();
    }
}

?>