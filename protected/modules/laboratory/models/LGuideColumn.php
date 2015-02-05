<?php

class LGuideColumn extends LModel {

	/**
	 * Returns the name of the associated database table.
	 * By default this method returns the class name as the table name.
	 * You may override this method if the table is not named after this convention.
	 * @return string the table name
	 */
	public function tableName() {
		return "lis.guide_column";
	}

	/**
	 * Get model's instance from cache
	 * @return LGuideColumn - Cached model instance
	 */
	public static function model() {
		return parent::model(__CLASS__);
	}

	/**
	 * Find all identification numbers for this table
	 * @param string $conditions - Search condition
	 * @param array $params - Array with parameters
	 * @return array - Array with identification numbers
	 * @throws CDbException
	 */
	public function findIds($conditions = '', $params = []) {
		$query = $this->getDbConnection()->createCommand()
			->select("id")
			->from($this->tableName())
			->where($conditions, $params);
		$array = [];
		foreach ($query->queryAll() as $a) {
			$array[] = $a["id"];
		}
		return $array;
	}
}