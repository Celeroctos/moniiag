<?php
class SheduleSettedBe extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.doctor_shedule_setted_be';
    }

    public function getOne($id) {
        try {


        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
	
	public function getRows($filters, $employeeId, $sidx = false, $sord = false, $start = false, $limit = false) {
		$connection = Yii::app()->db;
		$shedules = $connection->createCommand()
			->select('shedule.*')
			->from('mis.doctor_shedule_setted_be shedule')
			->where('employee_id = :id', array(':id' => $employeeId));

		if($filters !== false) {
			$this->getSearchConditions($shedules, $filters, array(

				), array(
				'shedule' => array('id')
				)
		);
		}

		if($sidx !== false && $sord !== false) {
			$shedules->order($sidx.' '.$sord);
		}

		if( $start !== false && $limit !== false) {
			$shedules->limit($limit, $start);
		}
		return $shedules->queryAll();
	}

    public function getByEnterprise($id) {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

}

?>