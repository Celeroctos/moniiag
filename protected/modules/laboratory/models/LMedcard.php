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
            ->queryAll();
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
                o.birthday as birthday")
            ->from("mis.medcards as m")
            ->join("mis.oms as o", "m.policy_id = o.id")
			->where($condition, $parameters);
    }

    /**
     * @return string - Name of table
     */
    public function tableName() {
        return "mis.medcards";
    }
} 