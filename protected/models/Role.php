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
            ->select('r.*, r2.name as parent, mp.name as startpage')
            ->from('mis.roles r')
            ->leftJoin('mis.roles r2', 'r.parent_id = r2.id')
            ->leftJoin('mis.menu_pages mp', 'r.startpage_id = mp.id');

        if($filters !== false) {
            $this->getSearchConditions($roles, $filters, array(
            ), array(

            ), array(
            ));
        }

        if($sidx !== false && $sord !== false) {
            $roles->order($sidx.' '.$sord);
        } else {
            $roles->order('r.id desc');
        }

        if($start !== false && $limit !== false) {
            $roles->limit($limit, $start);
        }

        return $roles->queryAll();
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $role = $connection->createCommand()
                ->select('r.*, mp.url, mp.priority')
                ->from('mis.roles r')
                ->leftJoin('mis.menu_pages mp', 'r.startpage_id = mp.id')
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

            $currentPriority = -1;
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