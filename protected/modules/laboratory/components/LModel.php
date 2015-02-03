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
	 * @return CDbCommand - Command with selection query
	 * @throws CDbException
	 */
	public function getTable() {
		return $this->getDbConnection()->createCommand()
			->select("*")
			->from($this->tableName());
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

		$users = $this->getJqGrid();

		if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
			$users->order($sidx.' '.$sord);
			$users->limit($limit, $start);
		}

		return $users->queryAll();
	}
} 