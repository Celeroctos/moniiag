<?php
class MedcardRule extends MisActiveRecord {
    public function getDbConnection(){
        return Yii::app()->db;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcards_rules';
    }
	
	public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $rules = $connection->createCommand()
            ->select('mr.*, mpr.value as prefix, mpo.value as postfix, mr2.id as parent, ms1.value as postfix_separator, ms2.value as prefix_separator')
            ->from(MedcardRule::tableName().' mr')
			->leftJoin(MedcardPrefix::tableName().' mpr', 'mpr.id = mr.prefix_id')
			->leftJoin(MedcardPostfix::tableName().' mpo', 'mpo.id = mr.postfix_id')
			->leftJoin(MedcardRule::tableName().' mr2', 'mr2.id = mr.parent_id')
			->leftJoin(MedcardSeparator::tableName().' ms1', 'mr.postfix_separator_id = ms1.id')
			->leftJoin(MedcardSeparator::tableName().' ms2', 'mr.prefix_separator_id = ms2.id');

        if($filters !== false) {
            $this->getSearchConditions($rules, $filters, array(
            ), array(
                'mr' => array('id', 'value', 'parent_id', 'prefix_id', 'postfix_id', 'participle_mode_prefix', 'participle_mode_postfix', 'postfix_separator_id', 'prefix_separator_id')
            ), array(
            ));
        }

        if($sidx !== false && $sord !== false) {
            $rules->order($sidx.' '.$sord);
        }
		if($start !== false && $limit !== false) {
			$rules->limit($limit, $start);
		}

        return $rules->queryAll();

    }
}

?>