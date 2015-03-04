<?php

class LDirection extends LModel {

	/**
	 * @return LDirection - Cached model instance
	 */
    public static function model() {
        return parent::model(__CLASS__);
    }

	/**
	 * Returns the name of the associated database table.
	 * By default this method returns the class name as the table name.
	 * You may override this method if the table is not named after this convention.
	 * @return string the table name
	 */
    public function tableName() {
        return "lis.direction";
    }

	/**
	 * Get data provider for CGridView widget
	 * @return CActiveDataProvider - Data provider
	 */
	public function getDataProvider() {
		$sort = new CSort($this);
		foreach ($this->getKeys() as $key) {
			$sort->attributes[$key] = [
				"desc" => "$key desc",
				"asc" => "$key"
			];
		}
		$provider = new CActiveDataProvider($this, [
			"pagination" => [
				"pageSize" => 20
			],
			"sort" => $sort
		]);
		return $provider;
	}

	/**
	 * Get array with keys for CGridView to display or order
	 * @return array - Array with model data
	 */
	private function getKeys() {
		return [ "id", "surname", "name", "patronymic", "card", "sender_id", "analysis_type_id" ];
	}
}