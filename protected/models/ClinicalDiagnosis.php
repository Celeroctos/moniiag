<?php
class ClinicalDiagnosis extends MisActiveRecord  {
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'mis.clinical_diagnosis';
	}

	public function getAll() {

	}

	public function primaryKey()
	{
		return 'id';
		// Для составного первичного ключа следует использовать массив:
		// return array('pk1', 'pk2');
	}

	public function getRows($filters, $needAll, $sidx = false, $sord = false, $start = false, $limit = false) {
		$connection = Yii::app()->db;
		$diagnosises = $connection->createCommand()
			->select('cd.*')
			->from($this->tableName().' cd');	
		// Если нужно только неудалённые
		if (!$needAll)
		{
			$diagnosises=$diagnosises->where('cd.is_deleted=0');
			
		}
		
		if($filters !== false) {
			$this->getSearchConditions($diagnosises, $filters, array(

				), array(
				'cd' => array('id', 'description')
				), array(

				));
		}

		if($sidx !== false && $sord !== false)
		{	
			$diagnosises->order($sidx.' '.$sord);
		}
		
		if ($start !== false && $limit !== false)
		{
			$diagnosises->limit($limit, $start);
		}

		return $diagnosises->queryAll();
	}
	
	public function getOne($id)
	{
		try {
			$connection = Yii::app()->db;
			$diagnosis = $connection->createCommand()
				->select('cd.*')
				->from('mis.clinical_diagnosis cd')
				->where('cd.id = :id', array(':id' => $id))
				->queryRow();

			return $diagnosis;

		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}
}