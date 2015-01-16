<?php

class User extends MisActiveRecord  {

    /**
     * @param string $className
     * @return User
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'mis.users';
    }

    /**
     * Fetch user from database with it's login and password, it will
     * crypt password with md5's md5 :D
     * @param $login string - User's login
     * @param $password - User's password (original)
     * @return mixed - Row with user's fields
     * @throws CDbException
     * @throws Exception
     */
    public function fetchByLoginAndPassword($login, $password) {
        $user = $this->getDbConnection()->createCommand()
            ->select("*")
            ->from("mis.users u")
            ->where("lower(u.login) = lower(:login) and u.password = :password")
            ->limit("1")
            ->queryRow([
                ":login" => $login,
                ":password" => md5(md5($password))
            ]);
        if ($user != null) {
            return $user;
        }
        throw new Exception("Can't resolve user's login or password ({$login})");
    }

    public function getAll() {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $users = $connection->createCommand()
            ->select('u.login, u.username, u.employee_id, u.id, CONCAT(d.last_name, \' \', d.first_name, \' \', d.middle_name) as employee')
            ->from('mis.users u')
            ->leftJoin('mis.doctors d', 'd.id = u.employee_id');

        if($filters !== false) {
            $this->getSearchConditions($users, $filters, array(
            ), array(
                'u' => array('id', 'username', 'login'),
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $users->order($sidx.' '.$sord);
            $users->limit($limit, $start);
        }

        $users = $users->queryAll();
        $numUsers = count($users);
        if($numUsers > 0) {
            // Выбираем роли
            for($i = 0; $i < $numUsers; $i++) {
                $rolesToUser = RoleToUser::model()->findAllRolesByUser($users[$i]['id']);
                $users[$i]['role_id'] = array();
                $users[$i]['rolename'] = '';
                foreach($rolesToUser as $role) {
                    $users[$i]['role_id'][] = $role['id'];
                    $users[$i]['rolename'] .= $role['name'].', ';
                }
                $users[$i]['rolename'] = mb_substr($users[$i]['rolename'], 0, mb_strlen($users[$i]['rolename']) - 2);
            }
        }
        return $users;
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $user = $connection->createCommand()
                ->select('u.login, u.username, u.employee_id, u.id')
                ->from('mis.users u')
                ->where('u.id = :id', array(':id' => $id))
                ->queryRow();

            if($user != null) {
                // Выбираем роли
                $rolesToUser = RoleToUser::model()->findAll('user_id = :user_id', array(':user_id' => $user['id']));
                $user['role_id'] = array();
                foreach($rolesToUser as $role) {
                    $user['role_id'][] = $role['role_id'];
                }
            }

            return $user;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}