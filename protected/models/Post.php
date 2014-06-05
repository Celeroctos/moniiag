<?php
class Post extends CActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medpersonal';
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $post = $connection->createCommand()
            ->select('p.*')
            ->from('mis.medpersonal p');

        if($filters !== false) {
            $this->getSearchConditions($post, $filters, array(
            ), array(
                'p' => array('id', 'name')
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false ) {
            $post->order($sidx.' '.$sord);
        }
        if($start !== false && $limit !== false) {
            $post->limit($limit, $start);
        }
        return $post->queryAll();
    }
}

?>