<?php
class Setting extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.settings';
    }


    public function getAll() {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $settings = $connection->createCommand()
            ->select('s.*')
            ->from('mis.settings s');

        if($filters !== false) {
            $this->getSearchConditions($settings, $filters, array(
            ), array(
                's' => array('module_id', 'id', 'name', 'value')
            ), array(
            ));
        }

        return $settings->queryAll();
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $role = $connection->createCommand()
                ->select('r.*')
                ->from('mis.roles r')
                ->where('r.id = :id', array(':id' => $id))
                ->queryRow();

            return $role;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>