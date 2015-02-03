<?php

class LGuide extends LModel {

	/**
	 * Returns the name of the associated database table.
	 * By default this method returns the class name as the table name.
	 * You may override this method if the table is not named after this convention.
	 * @return string the table name
	 */
	public function tableName() {
		return "lis.guide";
	}

	/**
	 * Get model's instance from cache
	 * @param string $className - Class's name
	 * @return LGuide - Cached model instance
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
}