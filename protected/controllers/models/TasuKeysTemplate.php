<?php
class TasuKeysTemplate extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.tasu_keys_templates_list';
    }

    public function getOne($id) {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $templates = $connection->createCommand()
            ->select('tktl.*')
            ->from(TasuKeysTemplate::tableName().' tktl');

        if($filters !== false) {
            $this->getSearchConditions($templates, $filters, array(

            ), array(
                'tftl' => array('id', 'name', 'template')
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