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
            ->select('u.login, u.username, u.employee_id, u.role_id, u.id, CONCAT(d.last_name, \' \', d.first_name, \' \', d.middle_name) as employee')
            ->from('mis.users u')
            ->leftJoin('mis.doctors d', 'd.id = u.employee_id');

        if($filters !== false) {
            $this->getSearchConditions($users, $filters, array(
            ), array(
                'u' => array('id', 'role_id', 'username', 'login'),
            ), array(
            ));
        }
        return $users->queryAll();
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $user = $connection->createCommand()
                ->select('u.login, u.username, u.employee_id, u.role_id, u.id')
                ->from('mis.users u')
                ->where('u.id = :id', array(':id' => $id))
                ->queryRow();

            return $user;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>