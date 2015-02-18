<?php

class LGuideRow extends LModel {

	public $id;
	public $guide_id;

	/**
	 * Returns the name of the associated database table.
	 * By default this method returns the class name as the table name.
	 * You may override this method if the table is not named after this convention.
	 * @return string the table name
	 */
	public function tableName() {
		return "lis.guide_row";
	}

	/**
	 * Find all values for current row, where every value will be
	 * associated with column's position and ordered by it
	 * @param $rowId int - Row's identification number
	 * @return array - Array with row's values
	 * @throws CDbException
	 */
	public function findValues($rowId) {
		$query = $this->getDbConnection()->createCommand()
			->select("c.position, v.*")
			->from("lis.guide_row as r")
			->join("lis.guide_value as v", "v.guide_row_id = r.id")
			->join("lis.guide_column as c", "c.id = v.guide_column_id")
			->where("r.id = :id")
			->order("c.position");
		return $query->queryAll(true, [
			":id" => $rowId
		]);
	}

	/**
	 * That method will find all values for current row, but only
	 * one field with column's id (display's id). Tt will also return
	 * columns parameters to simplify format processes
	 * @param int $rowId - Row's identification number
	 * @param int $displayId - Column's identification number to display
	 * @return array - Array with values
	 * @throws CDbException
	 */
	public function findValueWithDisplay($rowId, $displayId) {
		$query = $this->getDbConnection()->createCommand()
			->select("v.id as id, v.value as value, c.name as name, c.type as type, c.position as position")
			->from("lis.guide_row as r")
			->join("lis.guide_value as v", "v.guide_row_id = r.id")
			->join("lis.guide_column as c", "v.guide_column_id = c.id")
			->where("r.id = :row_id and c.id = :display_id");
		return $query->queryRow(true, [
			":row_id" => $rowId,
			":display_id" => $displayId
		]);
	}

	/**
	 * That method will find all values for current row, but only
	 * one field with column's id (display's id). Tt will also return
	 * columns parameters to simplify format processes
	 * @param int $rowId - Row's identification number
	 * @param int $display - Column's name to display
	 * @return array - Array with values
	 * @throws CDbException
	 */
	public function findValueWithDisplayByName($rowId, $display) {
		$query = $this->getDbConnection()->createCommand()
			->select("v.id as id, v.value as value, c.name as name, c.type as type, c.position as position")
			->from("lis.guide_row as r")
			->join("lis.guide_value as v", "v.guide_row_id = r.id")
			->join("lis.guide_column as c", "v.guide_column_id = c.id")
			->where("r.id = :row_id and regexp_replace(lower(c.name), '\\s', '') = regexp_replace(lower(:display), '\\s', '')");
		return $query->queryRow(true, [
			":row_id" => $rowId,
			":display" => $display
		]);
	}

	/**
	 * Get model's instance from cache
	 * @return LGuideRow - Cached model instance
	 */
	public static function model() {
		return parent::model(__CLASS__);
	}
}