<?php
class User extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.users';
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
            ->select('u.login, u.username, u.id')
            ->from('mis.users u');
            //->leftJoin('mis.doctors d', 'd.id = u.employee_id');
			//CONCAT(d.last_name, \' \', d.first_name, \' \', d.middle_name) as employee

        if($filters !== false) {
            $this->getSearchConditions($users, $filters, array(
            ), array(
                'u' => array('id', 'username', 'login'),
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false) {
            $users->order($sidx.' '.$sord);
        }
		
		if($start !== false && $limit !== false) {
			$users->limit($limit, $start);
		}

        $users = $users->queryAll();
        $numUsers = count($users);
        if($numUsers > 0) {
            // Выбираем роли
            for($i = 0; $i < $numUsers; $i++) {
				// Выбираем сотрудников к юзеру
				$employees = Doctor::model()->findAll('user_id = :user_id', array(':user_id' => $users[$i]['id']));
				$numEmployees = count($employees);
				$users[$i]['employee_id'] = array();
				$users[$i]['employee'] = '';
				for($j = 0; $j < $numEmployees; $j++) {
					$fio = $employees[$j]['last_name'].' '.$employees[$j]['first_name'].' '.($employees[$j]['middle_name'] == null ? '' : $employees[$j]['middle_name']);
					$users[$i]['employee_id'][] = array( 
						'id' => $employees[$j]['id'],
						'fio' => $fio
					);
					if($j < $numEmployees - 1) {
						$users[$i]['employee'] .= $fio.', ';
					} else {
						$users[$i]['employee'] .= $fio;
					}
				}
 			
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

?>