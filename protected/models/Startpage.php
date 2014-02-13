<?php
class Startpage extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.menu_pages';
    }


    public function getAll() {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $startpages = $connection->createCommand()
            ->select('s.*')
            ->from(Startpage::tableName().' s');

        if($filters !== false) {
            $this->getSearchConditions($startpages, $filters, array(
            ), array(

            ), array(
            ));
        }

        if($start !== false && $limit !== false) {
            $startpages->limit($limit, $start);
        }

        if($sidx !== false && $sord !== false) {
            $startpages->order($sidx.' '.$sord);
        }

        return $startpages->queryAll();
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $startpage = $connection->createCommand()
                ->select('s.*')
                ->from(Startpage::tableName().' s')
                ->where('s.id = :id', array(':id' => $id))
                ->queryRow();

            return $startpage;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>