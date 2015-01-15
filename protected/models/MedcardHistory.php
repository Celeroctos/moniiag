<?php
class MedcardHistory extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcards_history';
    }

	public function getMaxThroughNumberPerYear($year, $ruleId) {
		$connection = Yii::app()->db;
        $medcard = $connection->createCommand()
            ->select('m.to, CAST(SUBSTRING(m.to, 0, LENGTH(m.to) - 2) as INTEGER) as "partNumber"') // dirty fix
            ->from('mis.medcards_history m')
			->where('rule_id = :rule_id 
					AND SUBSTRING(CAST(m.reg_date as TEXT), 0, 5) = :year
					', array(':rule_id' => $ruleId, ':year' => $year))
			->order('partNumber desc');

		return $medcard->queryRow();
	}
	
	public function geLastNumberThrough($ruleId) {
		$connection = Yii::app()->db;
        $medcard = $connection->createCommand()
            ->select('m.to')
            ->from('mis.medcards_history m')
			->where('rule_id = :rule_id', array(':rule_id' => $ruleId))
			->order('m.id desc');

		return $medcard->queryRow();
	}
	
	public function getByPrefixPostfixAndOms($prefix, $postfix, $omsId, $rule) {
		$connection = Yii::app()->db;
        $medcard = $connection->createCommand()
            ->select('m.*')
            ->from('mis.medcards_history m')
			->where('m.rule_id = :rule_id 
					 AND m.policy_id = :policy_id', 
					 array(
						':rule_id' => $rule->id,
						':policy_id' => $omsId
					 ))
			->andWhere(array('like', 'm.to', $prefix.'%'.$postfix));

		return $medcard->queryRow();
	}
}

?>