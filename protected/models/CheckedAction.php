<?php
class CheckedAction extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.role_action';
    }


    public function getAll() {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {

    }

    public function getByRole($id) {
        try {
            $connection = Yii::app()->db;
            $checked = $connection->createCommand()
                ->select('ra.*, aa.accessKey')
                ->from('mis.role_action ra')
                ->join('mis.access_actions aa', 'ra.action_id = aa.id')
                ->where('ra.role_id = :id', array(':id' => $id));
            return $checked->queryAll();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // Удаление проставленных экшенов роли
    public function deleteByRole($id) {
        try {
            $connection = Yii::app()->db;
            $checked = $connection->createCommand()
            ->delete('mis.role_action', 'role_id =:role_id', array(':role_id' => $id));
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
	
	// Удаление проставленных частных экшенов у сотрудника
    public function deleteByEmployee($id) {
        try {
            $connection = Yii::app()->db;
            $checked = $connection->createCommand()
            ->delete('mis.role_action', 'employee_id = :employee_id', array(':employee_id' => $id));
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getOne($id) {
        try {


        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
	
	public function findAllWithKeysByEmployee($doctorId) {
		try {
            $connection = Yii::app()->db;
            $checked = $connection->createCommand()
                ->select('ra.*, aa.accessKey')
                ->from('mis.role_action ra')
                ->join('mis.access_actions aa', 'ra.action_id = aa.id')
                ->where('ra.employee_id = :employee_id', array(':employee_id' => $doctorId));
            return $checked->queryAll();
        } catch(Exception $e) {
            echo $e->getMessage();
        }
	
	}


}

?>