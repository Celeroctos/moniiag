<?php

class LDirection extends LModel {

	/**
	 * @return LDirection - Cached model instance
	 */
    public static function model() {
        return parent::model(__CLASS__);
    }

    /**
     * Override that method to return data for grid view
     * @throws CDbException
     * @return array - Array with fetched rows
     */
    public function getGridViewData() {
        $query = $this->getDbConnection()->createCommand()
            ->select("d.*, at.name as analysis_type_id, m.card_number")
            ->from("lis.direction as d")
            ->leftJoin("lis.analysis_types as at", "at.id = d.analysis_type_id")
            ->leftJoin("lis.medcard as m", "m.id = d.medcard_id");
        $array = $query->queryAll();
        foreach ($array as &$value) {
            $value["status"] = LDirectionStatusField::field()->getOption($value["status"]);
        }
        return $array;
    }

    /**
     * Get array with keys for CGridView to display or order
     * @return array - Array with model data
     */
    public function getKeys() {
        return [
            "id" => "№",
            "medcard_id" => "Номер карты",
            "status" => "Статус",
            "department_id" => "Направитель",
            "analysis_type_id" => "Тип анализа"
        ];
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
}