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
	
	public function getLastNumberByYear($code = false, $ruleId) {
        $connection = Yii::app()->db;
        $medcard = $connection->createCommand()
            ->select('m.*, CAST(SUBSTRING("m"."to", 0, (CHAR_LENGTH("m"."to") - 2)) as INTEGER) as "fx"')
            ->from('mis.medcards_history m');
        $medcard->andWhere(array('like', 'm.to', '%/'.$code))
				->andWhere('rule_id = :rule_id', array(':rule_id' => $ruleId))
                ->order('fx desc')
                ->limit(1, 0);

        return $medcard->queryRow();
    }

}

?>