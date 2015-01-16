<?php

abstract class LModel extends CActiveRecord {

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