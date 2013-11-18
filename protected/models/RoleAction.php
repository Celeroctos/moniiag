<?php
class RoleAction extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.access_actions';
    }


    public function getAll() {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $roleActions = $connection->createCommand()
            ->select('aa.*, aag.name as groupname')
            ->from('mis.access_actions aa')
            ->leftJoin('mis.access_actions_groups aag', 'aa.group = aag.id');

        if($filters !== false) {
            $this->getSearchConditions($roleActions, $filters, array(
            ), array(

            ), array(
            ));
        }

        return $roleActions->queryAll();
    }

    public function getOne($id) {
        try {


        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>