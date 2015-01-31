<?php

class LGuide extends LModel {

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