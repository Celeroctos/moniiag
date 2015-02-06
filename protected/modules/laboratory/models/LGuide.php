<?php

class LGuide extends LModel {

	public $id;
	public $name;

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
	 * That method will return values ordered by columns positions for
	 * current guide
	 * @param $guideId
	 * @return array
	 * @throws CDbException
	 */
	public function findValues($guideId) {
		$query = $this->getDbConnection()->createCommand()
			->select("id")
			->from("lis.guide_row")
			->where("guide_id = :guide_id");
		$rows = $query->queryAll(true, [
			":guide_id" => $guideId
		]);
		$values = [];
		foreach ($rows as $row) {
			$values[] = LGuideRow::model()->findValues(
				$row["id"]
			);
		}
		return $values;
	}

	public function findValuesWithDisplay($guideId, $displayId) {
		$query = $this->getDbConnection()->createCommand()
			->select("*")
			->from("lis.guide_row")
			->where("guide_id = :guide_id");
		$rows = $query->queryAll(true, [
			":guide_id" => $guideId
		]);
		$values = [];
		foreach ($rows as $row) {
			$values[] = LGuideRow::model()->findValueWithDisplay(
				$row["id"], $displayId
			);
		}
		return $values;
	}

	/**
	 * Get model's instance from cache
	 * @return LGuide - Cached model instance
	 */
	public static function model() {
		return parent::model(__CLASS__);
	}
}