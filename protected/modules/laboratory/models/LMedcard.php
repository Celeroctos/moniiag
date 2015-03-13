<?php

class LMedcard extends LModel {

	public $id;
	public $mis_medcard;
	public $sender_id;
	public $patient_id;
	public $card_number;
	public $enterprise_id;

	/**
	 * Override that method to return count of rows in table
	 * @param CDbCriteria $criteria - Search criteria
	 * @return int - Count of rows in current table
	 * @throws CDbException
	 */
	public function getTableCount(CDbCriteria $criteria = null) {
		$query = $this->getDbConnection()->createCommand()
			->select("count(1) as count")
			->from("lis.medcard as m")
			->join("lis.patient as p", "p.id = m.patient_id")
			->leftJoin("lis.analysis as a", "a.medcard_number = m.card_number");
		if ($criteria != null && $criteria instanceof CDbCriteria) {
			$query->andWhere($criteria->condition, $criteria->params);
		}
		return $query->queryRow()["count"];
	}

	/**
	 * Override that method to return command for table widget
	 * @return CDbCommand - Command with selection query
	 * @throws CDbException
	 */
	public function getTable() {
		return $this->getDbConnection()->createCommand()
			->select("
                m.card_number as number,
                p.sex as phone,
                p.surname || ' ' || p.name || ' ' || p.patronymic as fio,
                p.name as name,
                p.patronymic as patronymic,
                p.birthday as birthday,
                e.shortname as enterprise,
                cast(a.registration_date as date) as registration_date")
			->from("lis.medcard as m")
			->join("lis.patient as p", "p.id = m.patient_id")
			->leftJoin("lis.analysis as a", "a.medcard_number = m.card_number")
			->leftJoin("mis.enterprise_params as e", "e.id = m.enterprise_id");
	}

	/**
	 * @return string - Name of table
	 */
	public function tableName() {
		return "lis.medcard";
	}
} 