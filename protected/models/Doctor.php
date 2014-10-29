<?php

class Doctor extends CActiveRecord {

	/**
	 * @param string $className
	 * 		Current class's name
	 * @return CActiveRecord
	 * 		Parent's model
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string
	 * 		Doctor's table name
	 */
	public function tableName() {
		return 'mis.doctors';
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
	public function getDoctorByPk($pk) {
		return $this->getById($pk);
	}

	/**
	 * @param $id int
	 *      Doctor's identifier number, must be integer value
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return mixed|null
	 *      Found mixed doctor's object, return null if
	 *      doctor with that identifier doesn't exist
	 */
	public function getByID($id, $forPregnant = false) {
		try {
			assert(is_integer($id),
				"Identifier must be integer value");

			$doctor = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("id = :id")
				->bindParam(":id", $id);

			if ($forPregnant !== false) {
				$doctor->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}
			$doctor = $doctor->query();

			if (!$doctor->getRowCount()) {
				return null;
			}

			return $this->applyFio($doctor->read());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $name string
	 *      Doctor's name
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Found doctors
	 */
	public function getByName($name, $forPregnant = false) {
		try {
			assert(is_string($name),
				"Name must be string value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("first_name = :name")
				->bindParam(':name', $name);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $surname string
	 *      Doctor's surname
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Found doctors
	 */
	public function getBySurname($surname, $forPregnant = false) {
		try {
			assert(is_string($surname),
				"Surname must be string value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("second_name = :surname")
				->bindParam(':surname', $surname);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $patronymic string
	 *      Doctor's surname
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Found doctors
	 */
	public function getByPatronymic($patronymic, $forPregnant = false) {
		try {
			assert(is_string($patronymic),
				"Patronymic must be string value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("last_name = :patronymic")
				->bindParam(':patronymic', $patronymic);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $fio string
	 * 		Doctor's fio separated by whitespaces (/[\s,]+/)
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 * 		Found doctors
	 */
	public function getByFio($fio, $forPregnant = false) {
		try {
			assert(is_string($fio),
				"Fio must be string value");

			$fio = preg_split("/[\s,]+/", $fio);

			assert(count($fio) == 3,
				"Invalid Fio's format");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->andWhere("first_name = :n")
				->andWhere("middle_name = :s")
				->andWhere("last_name = :p")
				->bindParam(":n", $fio[0])
				->bindParam(":s", $fio[1])
				->bindParam(":p", $fio[2]);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $postId int
	 *      Doctor's post's identifier number
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Array with Found doctors
	 */
	public function getByPostID($postId, $forPregnant = false) {
		try {
			assert(is_integer($postId),
				"Post's identifier must be integer value");

			$result =  $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("post_id = :postId")
				->bindParam(':postId', $postId);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $tabelNumber int
	 *      Doctor's table number
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Array with Found doctors
	 */
	public function getByTabelNumber($tabelNumber, $forPregnant = false) {
		try {
			assert(is_integer($tabelNumber),
				"Tabel's number must be integer value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("tabel_number = :tabel_number")
				->bindParam(':tabel_number', $tabelNumber);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $degreeId int
	 *      Doctor's table number
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Array with Found doctors
	 */
	public function getByDegreeID($degreeId, $forPregnant = false) {
		try {
			assert(is_integer($degreeId),
				"Degree's identifier must be integer value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("degree_id = :degree_id")
				->bindParam(':degree_id', $degreeId);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $titulId int
	 *      Doctor's title identifier
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Array with Found doctors
	 */
	public function getByTitulID($titulId, $forPregnant = false) {
		try {
			assert(is_integer($titulId),
				"Titile's identifier must be integer value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("titul_id = :titul_id")
				->bindParam(':titul_id', $titulId);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $dateBegin string
	 *      Doctor's begin date
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Array with Found doctors
	 */
	public function getByBeginDate($dateBegin, $forPregnant = false) {
		try {
			assert(is_string($dateBegin),
				"Begin date must be string value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("date_begin = :date_begin")
				->bindParam(':date_begin', $dateBegin);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			));
			die;
		}
	}

	/**
	 * @param $dateEnd string
	 *      Doctor's end date
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 * 		Array with Found doctors
	 */
	public function getByEndDate($dateEnd, $forPregnant = false) {
		try {
			assert(is_string($dateEnd),
				"End date must be string value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("date_end = :date_end")
				->bindParam(':date_end', $dateEnd);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

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
	 *      Current date for doctor's range
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Array with Found doctors
	 */
	public function getBetweenDates($currentDate, $forPregnant = false) {
		try {
			assert(is_string($currentDate),
				"Current date must be string value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where(":date >= date_begin AND :date <= date_end")
				->bindParam(':date', $currentDate);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $wardCode int
	 *      Doctor's ward code
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Array with Found doctors
	 */
	public function getByWardCode($wardCode, $forPregnant = false) {
		try {
			assert(is_integer($wardCode),
				"Ward code must be string value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("ward_code = :ward_code")
				->bindParam(':ward_code', $wardCode);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $tasuId int
	 *      Doctor's identifier in TASU system
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Array with Found doctors
	 */
	public function getByTasuID($tasuId, $forPregnant = false) {
		try {
			assert(is_integer($tasuId),
				"Tasu identifier must be string value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("tasu_id = :tasu_id")
				->bindParam(':tasu_id', $tasuId);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	const DOCTOR_GREETING_ANY       = 0;
	const DOCTOR_GREETING_PRIMARY   = 1;
	const DOCTOR_GREETING_SECONDARY = 2;

	/**
	 * @param $greetingType int
	 *      Type of doctor's greeting :
	 *        - 0 : Any
	 *        - 1 : Primary
	 *        - 2 : Secondary
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Array with Found doctors
	 */
	public function getByGreetingType($greetingType, $forPregnant = false) {
		try {
			assert(is_integer($greetingType),
				"Greeting type must be string value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("greeting_type = :greeting_type")
				->bindParam(':greeting_type', $greetingType);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param $displayable
	 *      If state is true, then we will find only doctors
	 *      that can be displayed in callcenter, else others
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Array with Found doctors
	 */
	public function getByDisplayableInCallcenter($displayable, $forPregnant = false) {
		try {
			assert(is_bool($displayable),
				"Displayable state mus be boolean value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("display_in_callcenter = :display_in_callcenter")
				->bindParam(':display_in_callcenter', $displayable);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param int $categoryNumber
	 *      Doctor's category number
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 *      Array with Found doctor
	 */
	public function getByCategory($categoryNumber, $forPregnant = false) {
		try {
			assert(is_integer($categoryNumber),
				"Category number must be integer value");

			$result = $this->getDbConnection()->createCommand()
				->select()
				->from($this->tableName())
				->where("categorie = :categorie")
				->bindParam(':categorie', $categoryNumber);

			if ($forPregnant !== false) {
				$result->join("mis.medpersonal m", "d.post_id = m.id")
					->where("m.is_for_pregnants = 1");
			}

			return $this->applyFio($result->queryAll());

		} catch (Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param bool $forPregnant bool
	 * 		Pregnant state
	 * @return array
	 * 		Array with doctors
	 */
	public function getAll($forPregnant = false) {
		try {
			$doctors = $this->getDbConnection()->createCommand()
				->select('d.*')
				->from('mis.doctors d');

			if($forPregnant !== false) {
				$doctors->join('mis.medpersonal m', 'd.post_id = m.id')
					->where('m.is_for_pregnants = 1');
			}

			return $this->applyFio($doctors->queryAll());

		} catch(Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param bool $forPregnant
	 * 		Shall we search doctors with pregnant flag
	 * @return array
	 * 		Array with doctors
	 */
	public function getAllForSelect($forPregnant = false) {
		try {
			$doctors = $this->getDbConnection()->createCommand()
				->select('d.*')
				->from('mis.doctors d');

			if($forPregnant !== false) {
				$doctors->join('mis.medpersonal m', 'd.post_id = m.id')
					->where('m.is_for_pregnants = 1');
			}
			$doctors->order('d.last_name asc');

			return $this->applyFio($doctors->queryAll());

		} catch(Exception $e) {
			echo json_encode(array(
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine()
			)); die;
		}
	}

	/**
	 * @param array $filters
	 * @param bool  $sidx
	 * @param bool  $sord
	 * @param bool  $start
	 * @param bool  $limit
	 * @param array $choosedDiagnosis
	 * @param bool  $greetingDate
	 * @param int   $calendarType
	 * @param bool  $isCallCenter
	 * @return mixed
	 */
	public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false, $choosedDiagnosis = array(), $greetingDate = false, $calendarType = 0, $isCallCenter = false) {

		$doctor = $this->getDbConnection()->createCommand()
			->selectDistinct('d.*, w.name as ward, m.name as post')
			->from('mis.doctors d')
			->leftJoin('mis.wards w', 'd.ward_code = w.id')
			->leftJoin('mis.medpersonal m', 'd.post_id = m.id');

		if(count($choosedDiagnosis) > 0) {
			$doctor->leftJoin('mis.mkb10_distrib md', 'md.employee_id = d.id');
		}

		if($filters !== false) {
			$this->getSearchConditions($doctor, $filters, array(
			), array(
				'd' => array('id', 'first_name', 'last_name', 'middle_name', 'post_id', 'ward_code', 'greeting_type'),
				'm' => array('is_for_pregnants')
			), array(

			));
		}

		if(count($choosedDiagnosis) > 0) {
			$doctor->andWhere(array('in', 'md.mkb10_id', $choosedDiagnosis));
		}

		if($isCallCenter) {
			$doctor->andWhere('d.display_in_callcenter = 1');
		}
		$doctor->andWhere('m.is_medworker = 1');

		// Теперь нужно выяснить сотрудников, которые могут принимать в этот день
		if($greetingDate !== false && $greetingDate !== null) {
			// Теперь мы знаем, каких врачей выбирать, с каким днём
			if($calendarType == 0) {
				//SheduleSetted
				$doctorsPerDay = DoctorsTimetable::model()->getDoctorsPerDate($greetingDate);
			} else { // Это выбирает врачей в промежутке
				$doctorsPerDay = DoctorsTimetable::model()->getDoctorsPerDates($greetingDate);
			}
			$doctorIds = array();
			$num = count($doctorsPerDay);
			for($i = 0; $i < $num; $i++) {
				$doctorIds[] = $doctorsPerDay[$i]['employee_id'];
			}
			$doctor->andWhere(array('in', 'd.id', $doctorIds));
		}

		if ($sidx && $sord && $limit) {
			$doctor->order($sidx.' '.$sord)
				->limit($limit, $start);
		}

		$doctors = $doctor->queryAll();
		return $doctors;
	}

	/**
	 * @param array $filters
	 * @return array
	 */
	public function getDoctorStat($filters) {

		$doctor = $this->getDbConnection()->createCommand()
			->select('d.*, w.name as ward, w.id as ward_id, m.name as post, m.id as post_id, dsbd.greeting_type, dsbd.patient_day, dsbd.order_number, mc.reg_date')
			->from('mis.doctors d')
			->leftJoin('mis.wards w', 'd.ward_code = w.id')
			->leftJoin('mis.medpersonal m', 'd.post_id = m.id')
			->leftJoin(SheduleByDay::tableName().' dsbd', 'dsbd.doctor_id = d.id')
			->leftJoin('mis.medcards mc', 'dsbd.medcard_id = mc.card_number');

		if($filters !== false) {
			$this->getSearchConditions($doctor, $filters, array(
			), array(
				'd'    => array('doctor_id', 'id'),
				'm'    => array('medworker_id', 'id'),
				'w'    => array('ward_id', 'id'),
				'dsbd' => array('patient_day', 'patient_day_to', 'patient_day_from')
			), array(
				'doctor_id'        => 'id',
				'medworker_id'     => 'id',
				'ward_id'          => 'id',
				'patient_day_to'   => 'patient_day',
				'patient_day_from' => 'patient_day'
			));
		}
		$doctor->andWhere('dsbd.time_begin IS NOT NULL');
		$doctor->andWhere('m.is_medworker = 1');

		$doctors = $doctor->queryAll();
		$resultArr = array();

		foreach($doctors as $doctor) {

			// Несуществующее отделение
			if(!isset($resultArr[(string)$doctor['ward_id']])) {
				$resultArr[(string)$doctor['ward_id']] = array(
					'name' => $doctor['ward'] != null ? $doctor['ward'] : 'Неизвестное отделение',
					'numAllGreetings' => 0,
					'primaryPerWriting' => 0,
					'primaryPerQueue' => 0,
					'secondaryPerWriting' => 0,
					'secondaryPerQueue' => 0,
					'elements' => array()
				);
			}

			// Несуществующая специальность
			if(!isset($resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']])) {
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']] = array(
					'name' => $doctor['post'] != null ? $doctor['post'] : 'Неизвестная специальность',
					'numAllGreetings' => 0,
					'primaryPerWriting' => 0,
					'primaryPerQueue' => 0,
					'secondaryPerWriting' => 0,
					'secondaryPerQueue' => 0,
					'elements' => array()
				);
			}

			// Несуществующий врач
			if(!isset($resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']])) {
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']] = array(
					'name' => $doctor['last_name'].' '.$doctor['first_name'].' '.$doctor['middle_name'],
					'data' => array(
						'numAllGreetings' => 0,
						'primaryPerWriting' => 0,
						'primaryPerQueue' => 0,
						'secondaryPerWriting' => 0,
						'secondaryPerQueue' => 0
					)
				);
			}

			// 2. Первичные приемы – прием в тот же день, в который завели или перерегистрировали карту (дали новый номер)
			if($doctor['patient_day'] == $doctor['reg_date']) { // Первичный
				if($doctor['order_number'] == null) {
					$resultArr[(string)$doctor['ward_id']]['primaryPerWriting']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['primaryPerWriting']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['primaryPerWriting']++;
				} else {
					$resultArr[(string)$doctor['ward_id']]['primaryPerQueue']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['primaryPerQueue']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['primaryPerQueue']++;
				}
				// 3. Вторичные приемы – приемы в дни, отличающиеся от дня заведения/перерегистрации карты.
			} else { // Вторичный
				if($doctor['order_number'] == null) {
					$resultArr[(string)$doctor['ward_id']]['secondaryPerWriting']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['secondaryPerWriting']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['secondaryPerWriting']++;
				} else {
					$resultArr[(string)$doctor['ward_id']]['secondaryPerQueue']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['secondaryPerQueue']++;
					$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['secondaryPerQueue']++;
				}
			}
			$resultArr[(string)$doctor['ward_id']]['numAllGreetings']++;
			$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['numAllGreetings']++;
			$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['numAllGreetings']++;
		}

		return $resultArr;
	}

	/**
	 * @param array $filters
	 * @return array
	 */
	public function getMisStat($filters) {

		$doctor = $this->getDbConnection()->createCommand()
			->select('d.*, w.name as ward, w.id as ward_id, m.name as post, m.id as post_id, dsbd.greeting_type, dsbd.patient_day, dsbd.order_number, dsbd.time_end, dsbd.is_accepted, mc.reg_date')
			->from('mis.doctors d')
			->leftJoin('mis.wards w', 'd.ward_code = w.id')
			->leftJoin('mis.medpersonal m', 'd.post_id = m.id')
			->leftJoin(SheduleByDay::tableName().' dsbd', 'dsbd.doctor_id = d.id')
			->leftJoin('mis.medcards mc', 'dsbd.medcard_id = mc.card_number');

		if($filters !== false) {
			$this->getSearchConditions($doctor, $filters, array(
			), array(
				'd'    => array('doctor_id', 'id'),
				'm'    => array('medworker_id', 'id'),
				'w'    => array('ward_id', 'id'),
				'dsbd' => array('patient_day', 'patient_day_to', 'patient_day_from')
			), array(
				'doctor_id'        => 'id',
				'medworker_id'     => 'id',
				'ward_id'          => 'id',
				'patient_day_to'   => 'patient_day',
				'patient_day_from' => 'patient_day'
			));
		}

		$doctor->andWhere('dsbd.time_begin IS NOT NULL');
		$doctor->andWhere('m.is_medworker = 1');

		$dateBegin = false;
		$dateEnd = false;

		foreach($filters['rules'] as $filter) {
			if($filter['field'] == 'patient_day_from' && trim($filter['data']) != '') {
				$dateBegin = $filter['data'];
			}
			if($filter['field'] == 'patient_day_to' && trim($filter['data']) != '') {
				$dateEnd = $filter['data'];
			}
		}

		$doctors = $doctor->queryAll();
		$resultArr = array();

		foreach($doctors as $doctor) {

			// Несуществующее отделение
			if(!isset($resultArr[(string)$doctor['ward_id']])) {
				$resultArr[(string)$doctor['ward_id']] = array(
					'name' => $doctor['ward'] != null ? $doctor['ward'] : 'Неизвестное отделение',
					'numAllGreetings' => 0,
					'closedGreetings' => 0,
					'handworkGreetings' => 0,
					'elements' => array()
				);
			}
			// Несуществующая специальность
			if(!isset($resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']])) {
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']] = array(
					'name' => $doctor['post'] != null ? $doctor['post'] : 'Неизвестная специальность',
					'numAllGreetings' => 0,
					'closedGreetings' => 0,
					'handworkGreetings' => 0,
					'elements' => array()
				);
			}

			// Несуществующий врач
			if(!isset($resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']])) {
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']] = array(
					'name' => $doctor['last_name'].' '.$doctor['first_name'].' '.$doctor['middle_name'],
					'data' => array(
						'numAllGreetings'   => 0,
						'closedGreetings'   => 0,
						'handworkGreetings' => 0
					)
				);

				// Считаем приёмы, которые добавили вручную, для данного врача
				$numFakes = TasuFakeGreetingsBuffer::model()->getNumRows($doctor['id'], $dateBegin, $dateEnd);
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['handworkGreetings'] = $numFakes['num_greetings'];
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['handworkGreetings'] += $numFakes['num_greetings'];
				$resultArr[(string)$doctor['ward_id']]['handworkGreetings'] += $numFakes['num_greetings'];
			}

			if($doctor['is_accepted'] == 1) { // Закрытый приём вручную
				$resultArr[(string)$doctor['ward_id']]['closedGreetings']++;
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['closedGreetings']++;
				$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['closedGreetings']++;
			}

			$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['elements'][(string)$doctor['id']]['data']['numAllGreetings']++;
			$resultArr[(string)$doctor['ward_id']]['numAllGreetings']++;
			$resultArr[(string)$doctor['ward_id']]['elements'][(string)$doctor['post_id']]['numAllGreetings']++;
		}

		return $resultArr;
	}

	/**
	 * Add 'fio' field to every doctor
	 *
	 * @param $doctors array
	 * 		Array with doctors
	 * @return mixed
	 * 		Same array with doctors
	 */
	private function applyFio(&$doctors) {
		if (is_array($doctors)) {
			foreach($doctors as $key => &$doctor) {
				if (isset($doctors['fio'])) {
					continue;
				}
				$doctor['fio'] = $doctor['first_name'].' '.$doctor['middle_name'].' '.$doctor['last_name'];
			}
		} else {
			$doctors['fio'] = $doctors['first_name'].' '.$doctors['middle_name'].' '.$doctors['last_name'];
		}
		return $doctors;
	}
}