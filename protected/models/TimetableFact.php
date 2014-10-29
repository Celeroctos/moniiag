<?php

class TimetableFact extends CActiveRecord {

	/**
	 * @return string
	 * 		Timetable's table name
	 */
	public function tableName() {
		return 'mis.timetable_facts';
	}

	/**
	 * @return string
	 * 		Primary key's name
	 */
	public function primaryKey() {
		return 'id';
	}

	/**
	 * @param $pk int
	 * 		Primary key identifier
	 * @return mixed
	 * 		Mixed rule's object
	 */
	public function getTimetableByPk($pk) {
		return $this->getById($pk);
	}

	/**
	 * @param $id int
	 * 		Primary key identifier
	 * @return mixed
	 * 		Mixed rule's object
	 */
	public function getById($id) {
		try {
			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("id = :id")
				->bindParam(":id", $id);

			$result = $result->query();

			if (!$result->getRowCount()) {
				return null;
			}

			return $result->read();

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $id int
	 * 		Rule's identifier
	 * @return mixed
	 * 		Mixed rule's object
	 */
	public function getByTimetableId($id) {
		try {
			return $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("timetable_id = :timetable_id")
				->bindParam(":timetable_id", $id)
				->queryAll();

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $day int
	 *		Current date for fact's begin date (only if
	 * 		is_range is false)
	 * @return mixed
	 * 		Mixed rule's object
	 */
	public function getByDay($day) {
		try {
			return $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("is_range = false AND day = :begin_date")
				->bindParam(":begin_date", $day)
				->queryAll();

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $day int
	 * 		Current day between fact's range (only if is_range
	 * 		is true)
	 * @return mixed
	 * 		Mixed rule's object
	 */
	public function getByRange($day) {
		try {
			return $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("is_range = true AND :day >= begin_date AND :day <= end_date")
				->bindParam(":day", $day)
				->queryAll();

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $type int
	 * 		Fact's type
	 * @return mixed
	 * 		Mixed rule's object
	 */
	public function getByType($type) {
		try {
			return $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("type = :type")
				->bindParam(":type", $type)
				->queryAll();

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	public function getForSelect() {
		try {
			$facts = $this->getDbConnection()->createCommand()
				->select('*')
				->from(TimetableFact::tableName());

			return $facts ->queryAll();

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}
}