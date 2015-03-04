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
     * Override that method to return list with table
     * keys for CGridView widget
     * @return array - Array with keys names
     */
    public function getKeys() {
        return [];
    }

    /**
     * Override that method to return data for grid view
     * @throws CDbException
     * @return array - Array with fetched rows
     */
    public function getGridViewData() {
        $query = $this->getDbConnection()->createCommand()
            ->select("*")
            ->from($this->tableName());
        return $query->queryAll();
    }

    /**
     * Returns the attribute labels.
     * Attribute labels are mainly used in error messages of validation.
     * By default an attribute label is generated using {@link generateAttributeLabel}.
     * This method allows you to explicitly specify attribute labels.
     *
     * Note, in order to inherit labels defined in the parent class, a child class needs to
     * merge the parent labels with child labels using functions like array_merge().
     *
     * @return array attribute labels (name=>label)
     * @see generateAttributeLabel
     */
    public function attributeLabels() {
        return $this->getKeys();
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
			if (is_array($r)) {
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
     * @param string $pk - Primary key
     * @throws CDbException
     * @return array - Array with identification numbers
     */
	public function findIds($conditions = '', $params = [], $pk = "id") {
		$query = $this->getDbConnection()->createCommand()
			->select($pk)
			->from($this->tableName())
			->where($conditions, $params);
		$array = [];
		foreach ($query->queryAll() as $a) {
			$array[] = $a[$pk];
		}
		return $array;
	}

    /**
     * Get data provider for CGridView widget
     * @return CActiveDataProvider - Data provider
     */
    public function getDataProvider() {
        $criteria = new CDbCriteria();
        foreach ($this->getKeys() as $key => $ignored) {
            $criteria->compare($key, $this->$key, true, '');
        }
        return new LActiveDataProvider($this, [
            'criteria' => $criteria,
            'sort' => [
                'defaultOrder' => [
                    'id' => CSort::SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
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
	 * @param CDbCriteria $criteria - Search criteria
	 * @return int - Count of rows in current table
	 * @throws CDbException
	 */
	public function getTableCount(CDbCriteria $criteria = null) {
		$query = $this->getDbConnection()->createCommand()
			->select("count(*) as count")
			->from($this->tableName());
		if ($criteria != null && $criteria instanceof CDbCriteria) {
			$query->andWhere($criteria->condition, $criteria->params);
		}
		return $query->queryRow()["count"];
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