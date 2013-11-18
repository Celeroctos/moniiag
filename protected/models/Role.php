<?php
class Role extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.roles';
    }


    public function getAll() {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $roles = $connection->createCommand()
            ->select('r.*, r2.name as parent')
            ->from('mis.roles r')
            ->leftJoin('mis.roles r2', 'r.parent_id = r2.id');

        if($filters !== false) {
            $this->getSearchConditions($roles, $filters, array(
            ), array(

            ), array(
            ));
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

    public function getCurrentUserRole() {
        if(Yii::app()->user->isGuest) {
            return array(
                'id' => -1,
                'actions' => array() // У гостя можно предустановить опции
            );
        } else {
            $role = $this->getOne(Yii::app()->user->roleId);
            // Получим все экшены к роли
            $actionModel = new CheckedAction();
            $roleActions = $actionModel->getByRole(Yii::app()->user->roleId);
            $role['actions'] = array();
            foreach($roleActions as $key => $action) {
                $role['actions'][$action['action_id']] = $action['accessKey'];
            }
            return $role;
        }
    }
}

?>