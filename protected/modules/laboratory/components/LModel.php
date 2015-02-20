<?php

abstract class LModel extends CActiveRecord {

	/**
	 * Get model's instance from cache
	 * @param string $className - Class's name
	 * @return LGuide - Cached model instance
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

    /**
     * Find elements and format for drop down list
     * @param string $condition - List with condition
     * @param array $params - Query's parameters
     * @param string $pk - Name of primary key (or another value)
     * @return array - Array where every row associated with it's id
     */
	public function findForDropDown($condition = '', $params = array(), $pk = "id") {
        $result = $this->getDbConnection()->createCommand()
            ->select("*")
            ->from($this->tableName())
            ->where($condition, $params)
            ->queryAll();
		$select = [];
		foreach ($result as $r) {
			$select[$r[$pk]] = $this->populateRecord($r);
		}
		return $select;
	}

	/**
	 * Prepare array to drop down list
	 * @param array $array - Array with query results
	 * @param string $pk - Primary key name
	 * @return array - Array where every row associated with it's primary key
	 */
	public function toDropDown(array $array, $pk = "id") {
		$select = [];
		foreach ($array as $r) {
			if (!is_array($r)) {
				$r = $this->populateRecord($r);
			}
			$select[$r->$pk] = $r;
		}
		return $select;
	}

	/**
	 * Prepare array to drop down list
	 * @param array $array - Array with query results
	 * @param string $pk - Primary key name
	 * @return array - Array where every row associated with it's primary key
	 */
	public static function toDropDownStatic(array $array, $pk = "id") {
		$select = [];
		foreach ($array as $r) {
			if (is_array($r)) {
				$f = $r[$pk];
			} else {
				$f = $r->$pk;
			}
			$select[$f] = $r;
		}
		return $select;
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

	/**
	 * Override that method to return command for jqGrid
	 * @return CDbCommand - Command with query
	 * @throws CDbException
	 */
	public function getJqGrid() {
		return $this->getDbConnection()->createCommand()
			->select("*")
			->from($this->tableName());
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
			->select("*")
			->from($this->tableName())
			->where($condition, $parameters);
	}

	/**
	 * Override that method to return count of rows in table
	 * @return int - Count of rows in current table
	 * @throws CDbException
	 */
	public function getTableCount() {
		$row = $this->getDbConnection()->createCommand()
			->select("count(*) as count")
			->from($this->tableName())
			->queryRow();
		return $row["count"];
	}

	/**
	 * That method will return rows for jqGrid table
	 * @param bool $sidx - Sort index
	 * @param bool $sord - Sort order
	 * @param bool $start - Start index position
	 * @param bool $limit - Offset from start position
	 * @return array - Array with rows for jqGrid
	 */
	public function getRows($sidx = false, $sord = false, $start = false, $limit = false) {

		$query = $this->getJqGrid();

		if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
			$query->order($sidx.' '.$sord);
			$query->limit($limit, $start);
		}

		return $query->queryAll();
	}
} 