<?php
class EmkLog extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.emklog';
    }
	
	public function getLastLogByPolicy($policyId) {
        $connection = Yii::app()->db;
        $log = $connection->createCommand()
            ->select('e.*')
            ->from(EmkLog::tableName().' e')
			->where('e.policy_id = :policy_id', array(':policy_id' => $policyId))
			->order('e.id DESC')
			->limit(1, 0);

		return $log->queryRow();
	}
}

?>