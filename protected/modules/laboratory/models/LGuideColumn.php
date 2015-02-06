<?php

class LGuideColumn extends LModel {

	public $id;
	public $name;
	public $type;
	public $guide_id;
	public $lis_guide_id;
	public $position;
	public $display_id;

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
	 * Get displayable columns from database
	 * @param string $conditions - Search condition
	 * @param array $params - Array with parameters
	 * @return array - Array with displayable columns
	 * @throws CDbException
	 */
	public function findDisplayableAndOrdered($conditions = '', $params = []) {
		$query = $this->getDbConnection()->createCommand()
			->select("*")
			->from($this->tableName())
			->where("type <> 'dropdown' and type <> 'multiple'")
			->andWhere($conditions, $params)
			->order("position");
		return $query->queryAll();
	}

	/**
	 * Find all rows in table and order it by it's position
	 * @param string $conditions - Search condition
	 * @param array $params - Array with parameters
	 * @return array - Array with columns
	 * @throws CDbException
	 */
	public function findOrdered($conditions = '', $params = []) {
		$query = $this->getDbConnection()->createCommand()
			->select("*")
			->from($this->tableName())
			->where($conditions, $params)
			->order("position");
		return $query->queryAll();
	}

	/**
	 * Get model's instance from cache
	 * @return LGuideColumn - Cached model instance
	 */
	public static function model() {
		return parent::model(__CLASS__);
	}
}