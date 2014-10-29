<?php

class Timetable extends CActiveRecord {

	/**
	 * @return string
	 * 		Timetable's table name
	 */
	public function tableName() {
		return 'mis.timetable';
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
		return $this->getByID($pk);
	}

	/**
	 * @param $id int
	 * 		Timetable's identifier number
	 * @return mixed
	 * 		Mixed object with Found object
	 */
	public function getByID($id) {
		try {
			assert(is_integer($id),
				"Identifier must be integer value	");

			$timetable = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("id = :id")
				->bindParam(":id", $id)
				->query();

			if (!$timetable->getRowCount()) {
				return null;
			}

			return $timetable->read();

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $beginDate string
	 * 		Timetable's begin date
	 * @return array
	 * 		Array with Found timetables
	 */
	public function getByBeginDate($beginDate) {
		try {
			assert(is_string($beginDate),
				"Begin date must be string value");

			return $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("date_begin = :date_begin")
				->bindParam(":date_begin", $beginDate)
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
	 * @param $endDate string
	 * 		Timetable's begin date
	 * @return array
	 * 		Array with Found timetables
	 */
	public function getByEndDate($endDate) {
		try {
			assert(is_string($endDate),
				"End date must be string value");

			return $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("date_end = :date_end")
				->bindParam(":date_end", $endDate)
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
	 * @param $currentDate string
	 * 		Timetable's begin date
	 * @return array
	 * 		Array with Found timetables
	 */
	public function getBetweenDates($currentDate) {
		try {
			assert(is_string($currentDate),
				"Current date must be string value");

			return $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where(":date >= date_begin AND :date <= date_end")
				->bindParam(":date", $currentDate)
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
	 * @param $doctorId string
	 * 		Doctor's identifier number
	 * @return array
	 * 		Array with Found timetables
	 */
	public function getByDoctorID($doctorId) {
		try {
			assert(is_integer($doctorId),
				"Doctor's identifier must be integer value");

			return $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where(":doctor_id = doctor_id")
				->bindParam(":doctor_id", $doctorId)
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
	 * Find all doctors, which work in
	 * current date
	 *
	 * @param $beginDate string
	 * 		Begin workday
	 * @param $endDate string
	 * 		End workday
	 * @param $useFacts bool
	 * 		Shall we use doctor's facts
	 * @param $tableFilter array
	 * 		Don't confuse with $filter in MisActiveRecord/getSearchConditions!
	 * 		filter {
	 * 			rules {
	 *				weekday [array|integer]
	 * 				begin_time [string]
	 * 				end_time [string]
	 * 			}
	 * 			limits {
	 *				quantity [integer]
	 * 				time [string]
	 * 			}
	 * 		}
	 * 		It has another filters, cuz we will attach tables
	 * 		to it's query dynamically (performance ftw)
	 *
	 * @return array
	 * 		Array with found doctors
	 */
	public function getDoctorsByRange($beginDate, $endDate, $useFacts = true, $tableFilter = null) {
		try {
			assert(is_string($beginDate) && is_string($endDate),
				"Begin/End dates must be string value");

			$query = $this->getDbConnection()->createCommand()
				/*
				 * We will select only doctors
				 * from this query
				 */
				->selectDistinct("d.*")
				/*
				 * Basic table is doctor's timetable
				 * with doctor's identifier (reference)
				 */
				->from("mis.timetable as t")
				/*
				 * Default join is doctors that has
				 * attached to this timetable
				 */
				->leftJoin("mis.doctors as d",
					"t.doctor_id = d.id");

			/*
			 * Join table with rules only if we
			 * have something in table filter
			 */
			if ($tableFilter !== null && count($tableFilter) > 0) {
				$query->leftJoin("mis.timetable_rules as tr",
					"tr.timetable_id = t.id");
			} else {
				if ($useFacts === false) {
					return $query->queryAll();
				}
			}

			$this->applyFilters($query, $tableFilter);

			if ($useFacts === true) {
				$this->applyFacts($query, $beginDate, $endDate);
			}

			return $query->query();

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param string $date
	 * 		Current date
	 * @param bool   $useFacts
	 * 		Shall we use facts in search
	 * @param array  $tableFilter
	 *		Array with filters (description upper)
	 * @return array
	 * 		Found doctors
	 */
	public function getDoctorsByDate($date, $useFacts = true, $tableFilter = null) {
		return $this->getDoctorsByRange($date, $date, $useFacts, $tableFilter);
	}

	/**
	 * @param string $beginDate
	 * 		Current date
	 * @param bool   $useFacts
	 * 		Shall we use facts in search
	 * @param array  $tableFilter
	 *		Array with filters (description upper)
	 * @return array
	 * 		Found doctors
	 */
	public function getDoctorsByWeek($beginDate, $useFacts = true, $tableFilter = null) {

		assert(is_string($beginDate),
			"Date must be string value");

		$dt = DateTime::createFromFormat(
			"Y-m-d", $beginDate);

		$endDate = $dt->setTimestamp($dt->getTimestamp() + 7 * 24 * 3600)
			->format("Y-m-d");

		return $this->getDoctorsByRange($beginDate, $endDate, $useFacts, $tableFilter);
	}

	/**
	 * @param array $filters
	 * @param bool $sidx
	 * @param bool $sord
	 * @param bool $start
	 * @param bool $limit
	 * @return int
	 */
	public function getNumRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
		return count($this->getRowsIds($filters, $sidx, $sord, $start, $limit));
	}

	/**
	 * @param array $filters
	 * @param bool  $sidx
	 * @param bool  $sord
	 * @param bool  $start
	 * @param bool  $limit
	 * @return array
	 */
	private function getRowsIds($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
		try {
			$timetables = $this->getDbConnection()->createCommand()
				->selectDistinct('tt.id, tt.date_end')
				->from(Timetable::tableName().' tt')
				->join(Doctor::tableName(). ' d', 'd.id = tt.doctor_id')
				->leftJoin(Ward::tableName(). ' w', 'w.id = d.ward_code');

			// Смотрим - если заданы врачи - пришпиливаем к запросу врачей
			if ($filters['doctorsIds'] != NULL) {
				$timetables->andWhere('d.id in ('. implode(',',$filters['doctorsIds']) .')');
			} else {
				// Иначе - проверяем флаг "без отделения"
				if ($filters['woWardFlag'] == true) {
					$timetables->andWhere('d.ward_code is NULL');
				} else {
					if ($filters['wardsIds'] != NULL) {
						// Иначе проверяем заданные отделения
						$timetables->andWhere('d.ward_code in ('. implode(',',$filters['wardsIds']) .')');
					}
				}
			}

			// Если заданы даты начала и даты конца действия расписания - то учитываем эти условия
			if ((isset($filters['dateBegin'])) && (isset($filters['dateEnd']))) {
				$timetables->andWhere('(
						(tt.date_begin <= :begin AND tt.date_end >= :end)   OR
						(tt.date_begin <= :begin AND tt.date_end >= :begin) OR
						(tt.date_begin <= :end   AND tt.date_end >= :end)   OR
						(tt.date_begin >= :begin AND tt.date_end <= :end))',
					array(
						':begin' => $filters['dateBegin'],
						':end'   => $filters['dateEnd']
					)
				);
			}

			if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
				$timetables->order($sidx. ', tt.date_end '.$sord);
				$timetables->limit($limit, $start);
			}

			return $timetables->queryAll();

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param CDbCommand $query
	 * 		Query object with future results
	 * @param array      $tableFilter
	 * 		Filters with timetable conditions
	 */
	private function applyFilters($query, $tableFilter) {

		/*
		* Check timetable rules, likes weekdays, cabinet
		* or begin and end times. It will join all necessary
		* tables to query and append where statements to it
		*/
		if (isset($tableFilter['rules']) && count($tableFilter['rules'])) {

			$rules = $tableFilter['rules'];

			assert(is_array($rules),
				"Rules must have array type");

			/*
			 * We will search only that doctors
			 * that can assume patients in set weekdays
			 */
			if (isset($rules['weekday']) && count($rules['weekday'])) {

				$query->leftJoin("mis.timetable_rule_date as trd",
					"trd.rule_id = tr.id");

				$weekdays = $rules['weekday'];

				if (is_array($weekdays)) {
					$query->andWhere("trd.day in (".implode(",", $weekdays).")");
				} else if (is_integer($weekdays)) {
					$query->andWhere(":day = trd.day", array(
						":day" => $weekdays
					));
				} else {
					assert(true, "Weekday can only be integer or array value");
				}
			}
		}

		/*
		 * Also we can check out doctor's limits
		 * likes begin/end time limit and patient's quantity
		 */
		if (isset($tableFilter['limits']) && count($tableFilter['limits'])) {

			$query->leftJoin("mis.timetable_rule_limit as trl",
				"trl.rule_id = tr.id");

			$limits = $tableFilter['limits'];

			/*
			 * Check out doctor's quantity
			 * limit
			 */
			if (isset($limits['quantity'])) {
				$query->andWhere("trl.quantity is not null")
					->andWhere(":quantity >= trl.quantity", array(
						":quantity" => $limits['quantity']
					));
			}

			/*
			 * Check out doctor's time limit, only
			 * if it on track
			 */
			if (isset($limits['time'])) {
				$query->andWhere("trl.begin_time is not null and trl.end_time trl.end_time is not null")
					->andWhere(":time >= trl.begin_time and :time <= trl.end_time", array(
						":time" => $limits['time']
					));
			}
		}
	}

	/**
	 * @param CDbCommand $query
	 *      Query object with future results
	 * @param string $beginDate
	 * 		Begin date
	 * @param string|null $endDate
	 * 		End date
	 */
	private function applyFacts($query, $beginDate, $endDate = null) {

		if ($endDate === null) {
			$endDate = $beginDate;
		}

		$query->leftJoin("mis.timetable_facts as tf", "tf.timetable_id = t.id")
			->andWhere("(tf.is_range is null or case
				when tf.is_range = true then
					:begin_date < tf.begin_date and :end_date > tf.end_date
				else
					:begin_date < tf.begin_date and :end_date > tf.begin_date end)",
			array(
				":begin_date" => $beginDate,
				":end_date"   => $endDate
			));
	}

	/**
	 * @param string $dayToCount
	 * 		Day to count
	 * @return int
	 */
	private function computeWeekNumber($dayToCount) {
		/*
		 * НеделяМесяца = ( День(ЗаданнаяДата) +
		 * НомерДняНедели( ПервыйДеньМесяца(ЗаданнаяДата) ) - 2 ) целочисленное_деление_на 7 + 1
		*/
		return ((int)((date("j", strtotime($dayToCount)) + date("w", strtotime(
				date("m", strtotime($dayToCount))."/01/".
				date("Y", strtotime($dayToCount)))) - 2) / 7)) + 1;
	}

	/**
	 * @param array $filters
	 * @param bool  $sidx
	 * @param bool  $sord
	 * @param bool  $start
	 * @param bool  $limit
	 */
	public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
		try {
			$timetables = $this->getDbConnection()->createCommand()
				->selectDistinct('tt.*')
				->from(Timetable::tableName().' tt')
				->join(Doctor::tableName(). ' d', 'd.id = tt.doctor_id')
				->leftJoin(Ward::tableName(). ' w', 'w.id = d.ward_code');

			// Смотрим - если заданы врачи - пришпиливаем к запросу врачей
			if ($filters['doctorsIds'] != NULL) {
				$timetables->andWhere('d.id in ('. implode(',',$filters['doctorsIds']) .')');
			} else {
				// Иначе - проверяем флаг "без отделения"
				if ($filters['woWardFlag'] == true) {
					$timetables->andWhere('d.ward_code is NULL');
				} else {
					if ($filters['wardsIds'] != NULL) {
						// Иначе проверяем заданные отделения
						$timetables->andWhere('d.ward_code in ('. implode(',',$filters['wardsIds']) .')');
					}
				}
			}

			// Если заданы даты начала и даты конца действия расписания - то учитываем эти условия
			if ((isset($filters['dateBegin'])) && (isset($filters['dateEnd']))) {
				$timetables->andWhere('(
						(tt.date_begin <= :begin AND tt.date_end >= :end)   OR
						(tt.date_begin <= :begin AND tt.date_end >= :begin) OR
						(tt.date_begin <= :end   AND tt.date_end >= :end)   OR
						(tt.date_begin >= :begin AND tt.date_end <= :end))',
					array(
						':begin' => $filters['dateBegin'],
						':end'   => $filters['dateEnd']
					)
				);
			}

			if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
				$timetables->order($sidx. ', tt.date_end '.$sord);
				$timetables->limit($limit, $start);
			}

			return $timetables->queryAll();

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}
}