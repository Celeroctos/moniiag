<?php
class MenuPage extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.menu_pages';
    }

    public function getOne($id) {
        try {


        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {

    }

    public function getAll() {
        try {
            $connection = Yii::app()->db;
            $pages = $connection->createCommand()
                ->select('t.*')
                ->from(MenuPage::tableName().' t')
                ->order('t.name asc');

            return $pages->queryAll();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

}

?>