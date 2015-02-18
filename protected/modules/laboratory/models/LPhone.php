<?php

class LPhone extends LModel {

	public function tableName() {
		return "lis.phone";
	}

	/**
	 * Get model's instance from cache
	 * @return LPhone - Cached model instance
	 */
	public static function model() {
		return parent::model(__CLASS__);
	}
}