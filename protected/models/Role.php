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

    public function getCurrentUserRoles() {
        if(Yii::app()->user->isGuest) {
            return array(
                'id' => -1,
                'actions' => array() // У гостя можно предустановить опции
            );
        } else {
            $roles = Yii::app()->user->roleId; // Это массив ролей
            $numRoles = count($roles);
            $role = array(
                'id' => $roles[0]['id'], // В качестве id возьмём ID первой роли. Это неважно.
                'actions' => array()
            );
            for($i = 0; $i < $numRoles; $i++) {
                // Получим все экшены к роли
                $actionModel = new CheckedAction();
                $roleActions = $actionModel->getByRole($roles[$i]['id']);
                foreach($roleActions as $key => $action) {
                    $role['actions'][$action['action_id']] = $action['accessKey'];
                }
            }
            return $role;
        }
    }
}

?>