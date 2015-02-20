<?php

class LMedcard extends LModel {

    /**
     * @param string $className - Name of class to load
     * @return LMedcard - Model instance
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function fetchListWithPatients() {
        return $this->getDbConnection()->createCommand()
            ->select("m.card_number as number, m.contact as phone, o.first_name as name, o.middle_name as surname, o.last_name as patronymic")
            ->from("mis.medcards as m")
            ->join("mis.oms as o", "m.policy_id = o.id")
			->leftJoin("lis.analysis as a", "a.medcard_number = m.card_number")
            ->queryAll();
    }

	/**
	 * Override that method to return count of rows in table
	 * @param CDbCriteria $criteria - Search criteria
	 * @return int - Count of rows in current table
	 * @throws CDbException
	 */
	public function getTableCount(CDbCriteria $criteria = null) {
		$query = $this->getDbConnection()->createCommand()
			->select("count(*) as count")
			->from("mis.medcards as m")
			->join("mis.oms as o", "m.policy_id = o.id")
			->leftJoin("lis.analysis as a", "a.medcard_number = m.card_number");
		if ($criteria != null && $criteria instanceof CDbCriteria) {
			$query->andWhere($criteria->condition, $criteria->params);
		}
		return $query->queryRow()["count"];
	}

	/**
	 * Override that method to return command for table widget
	 * @param string $condition - Where conditions
	 * @param array $parameters - Query parameters
	 * @return CDbCommand - Command with selection query
	 * @throws CDbException
	 */
    public function getTable($condition = "", $parameters = []) {
        return $this->getDbConnection()->createCommand()
            ->select("
                m.card_number as number,
                m.contact as phone,
                o.first_name as name,
                o.middle_name as fio,
                o.last_name as patronymic,
                o.birthday as birthday,
                cast(a.registration_date as date) as registration_date")
            ->from("mis.medcards as m")
            ->join("mis.oms as o", "m.policy_id = o.id")
			->leftJoin("lis.analysis as a", "a.medcard_number = m.card_number")
			->where($condition, $parameters);
    }

    /**
     * @return string - Name of table
     */
    public function tableName() {
        return "mis.medcards";
    }
} 