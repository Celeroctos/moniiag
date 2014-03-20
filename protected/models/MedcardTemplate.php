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

    public function getTemplatesByEmployee($id)
    {
         try {
            $connection = Yii::app()->db;
            $templates = $connection->createCommand()
            ->select('mt.*')
            ->from('mis.medcard_templates mt')
            ->join('mis.medpersonal_templates mpt', 'mpt.id_template = mt.id')
            ->where('mpt.id_medpersonal = :medpersonal_id', array(':medpersonal_id' => $id));                
            return $templates->queryAll();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function getTemplatesByPageId($id) {
        try {
            $connection = Yii::app()->db;
            $templates = $connection->createCommand()
                ->select('mt.*')
                ->from('mis.medcard_templates mt')
                ->where('mt.page_id = :page_id', array(':page_id' => $id));

            return $templates->queryAll();

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