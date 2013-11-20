<?php
class MedcardTemplate extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcard_templates';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $template = $connection->createCommand()
                ->select('mt.*')
                ->from('mis.medcard_templates mt')
                ->where('mt.id = :id', array(':id' => $id))
                ->queryRow();

            return $template;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $templates = $connection->createCommand()
            ->select('me.*')
            ->from('mis.medcard_templates me');

        if($filters !== false) {
            $this->getSearchConditions($templates, $filters, array(

            ), array(
                'me' => array('id', 'name', 'page', 'categorie'),
            ), array(

            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $templates->order($sidx.' '.$sord);
            $templates->limit($limit, $start);
        }

        return $templates->queryAll();
    }
}

?>