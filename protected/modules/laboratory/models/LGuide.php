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

	/**
	 * Build an array with all guide's values searched by guide's name
	 * and column's name to display (all fields should be in russian ftw)
	 * @param string $guideName - Name of guide to search
	 * @param string $columnName - Name of column to display
	 * @return array - Array with guide values
	 * @throws CException
	 */
	public function findValuesWithDisplayByName($guideName, $columnName) {
		$guide = $this->find("regexp_replace(lower(name), '\\s', '') = regexp_replace(lower('{$guideName}'), '\\s', '')");
		if ($guide == null) {
			throw new CException("Справочник с таким именем не существует \"{$guideName}\"");
		}
		$query = $this->getDbConnection()->createCommand()
			->select("id")
			->from("lis.guide_row")
			->where("guide_id = :guide_id");
		$rows = $query->queryAll(true, [
			":guide_id" => $guide->id
		]);
		$values = [];
		foreach ($rows as $row) {
			$list = LGuideRow::model()->findValueWithDisplayByName(
				$row["id"], $columnName
			);
			$values[$list["id"]] = $list["value"];
		}
		return $values;
	}

	/**
	 * Find values for guide by it's display column identification
	 * number, it will skip values from another columns
	 * @param int $guideId - Guide identification number
	 * @param int $displayId - Column identification number
	 * @return array - Array with guide values
	 * @throws CDbException
	 */
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