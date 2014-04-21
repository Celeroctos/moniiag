<?php
class RoleToUser extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.role_to_user';
    }


    public function getAll() {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $roles = $connection->createCommand();

        if($filters !== false) {
            $this->getSearchConditions($roles, $filters, array(
            ), array(

            ), array(
            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $roles->order($sidx.' '.$sord);
            $roles->limit($limit, $start);
        } else {
            $roles->order('r.id desc');
        }

        return $roles->queryAll();
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

    public function findAllRolesByUser($userId) {
        try {
            $connection = Yii::app()->db;
            $role = $connection->createCommand()
                ->selectDistinct('r.*, mp.priority, mp.url')
                ->from('mis.roles r')
                ->leftJoin('mis.role_to_user ru', 'ru.role_id = r.id')
                ->leftJoin('mis.menu_pages mp', 'mp.id = r.startpage_id')
                ->where('ru.user_id = :user_id', array(':user_id' => $userId));

            $roles = $role->queryAll();

            return $roles;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>