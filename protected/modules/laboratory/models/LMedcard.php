<?php

class LMedcard extends LModel {

	public $privilege_code;
	public $snils;
	public $address;
	public $address_reg;
	public $doctype;
	public $serie;
	public $docnumber;
	public $gived_date;
	public $contact;
	public $invalid_group;
	public $card_number;
	public $enterprise_id;
	public $policy_id;
	public $reg_date;
	public $work_place;
	public $work_address;
	public $post;
	public $profession;
	public $motion;
	public $address_str;
	public $address_reg_str;
	public $user_created;

    /**
     * @param string $className - Name of class to load
     * @return LMedcard - Model instance
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

	/**
	 * Find information about patient by it's card number
	 * @param string $number - Number of patients card (mis.medcards.card_number)
	 * @return mixed - Mixed object with found row
	 * @throws CDbException
	 */
	public function fetchByNumber($number) {
		return $this->getDbConnection()->createCommand()
			->select("*")
			->from("mis.medcards as m")
			->join("mis.oms as o", "m.policy_id = o.id")
			->where("m.card_number = :card_number")
			->queryRow(true, [
				":card_number" => $number
			]);
	}

	/**
	 * Fetch list with patients and it's oms information
	 * @return array - Array with patients
	 * @throws CDbException
	 */
    public function fetchListWithPatients() {
        return $this->getDbConnection()->createCommand()
            ->select("m.card_number as number, m.contact as phone, o.first_name as name, o.middle_name as surname, o.last_name as patronymic")
            ->from("mis.medcards as m")
            ->join("mis.oms as o", "m.policy_id = o.id")
			->leftJoin("lis.analysis as a", "a.medcard_number = m.card_number")
            ->queryAll();
    }

	/**
	 * Fetch full information about medcard with patient info and others
	 * @param string $number - Number of medcard to find
	 * @return mixed - Found rows
	 * @throws CDbException
	 * @throws CException
	 */
	public function fetchInformation($number) {
		$row = $this->getDbConnection()->createCommand()
			->select("*")
			->from("mis.medcards as m")
			->join("mis.oms as o", "m.policy_id = o.id")
			->where("m.card_number = :number", [
				":number" => $number
			])->queryRow();
		if (!$row) {
			return null;
		}
		$row["address"] = $this->getAddressInfo(
			json_decode($row["address"], true)
		);
		$row["address_reg"] = $this->getAddressInfo(
			json_decode($row["address_reg"], true)
		);
		return $row;
	}

	/**
	 * Fetch information about address from cladr
	 * @param array $row - Array with parsed json object from [mis.medcards.address]
	 * @return array - Array with received information about address
	 * @throws CDbException - Database exceptions
	 * @throws CException - If some identification number broken
	 */
	protected function getAddressInfo($row) {
		$address = [
			"region" => null,
			"district" => null,
			"street" => null,
			"house" => null,
			"flag" => null,
			"building" => null,
			"post" => null
		];
		if (isset($row["regionId"])) {
			$region = $this->getDbConnection()->createCommand()
				->select("*")
				->from("mis.cladr_regions")
				->where("id = :id", [
					":id" => $row["regionId"]
				])
				->queryRow();
			if (!$region) {
				throw new CException("Unresolved region identification number \"{$row["regionId"]}\"");
			}
			$address["region"] = $region;
		}
		if (isset($row["districtId"])) {
			$district = $this->getDbConnection()->createCommand()
				->select("*")
				->from("mis.cladr_districts")
				->where("id = :id", [
					":id" => $row["districtId"]
				])
				->queryRow();
			if (!$district) {
				throw new CException("Unresolved region identification number \"{$row["districtId"]}\"");
			}
			$address["district"] = $district;
		}
		if (isset($row["streetId"])) {
			$street = $this->getDbConnection()->createCommand()
				->select("*")
				->from("mis.cladr_streets")
				->where("id = :id", [
					":id" => $row["streetId"]
				])
				->queryRow();
			if (!$street) {
				throw new CException("Unresolved street identification number \"{$row["streetId"]}\"");
			}
			$address["street"] = $street;
		}
		return $address + $row;
	}

	/**
	 * Override that method to return count of rows in table
	 * @param CDbCriteria $criteria - Search criteria
	 * @return int - Count of rows in current table
	 * @throws CDbException
	 */
	public function getTableCount(CDbCriteria $criteria = null) {
		$query = $this->getDbConnection()->createCommand()
			->select("count(1) as count")
			->from("mis.medcards as m")
			->join("mis.oms as o", "m.policy_id = o.id")
			->leftJoin("lis.analysis as a", "a.medcard_number = m.card_number");
		if ($criteria != null && $criteria instanceof CDbCriteria) {
			$query->andWhere($criteria->condition, $criteria->params);
		}
		return $query->queryRow()["count"];
	}

	/**
	 * Override that method to return command for table widget
	 * @param string $condition - Where conditions
	 * @param array $parameters - Query parameters
	 * @return CDbCommand - Command with selection query
	 * @throws CDbException
	 */
    public function getTable($condition = "", $parameters = []) {
        return $this->getDbConnection()->createCommand()
            ->select("
                m.card_number as number,
                m.contact as phone,
                o.first_name as name,
                o.middle_name as fio,
                o.last_name as patronymic,
                o.birthday as birthday,
                e.shortname as enterprise,
                cast(a.registration_date as date) as registration_date")
            ->from("mis.medcards as m")
            ->join("mis.oms as o", "m.policy_id = o.id")
			->leftJoin("lis.analysis as a", "a.medcard_number = m.card_number")
			->leftJoin("mis.enterprise_params as e", "e.id = m.enterprise_id")
			->where($condition, $parameters);
    }

    /**
     * @return string - Name of table
     */
    public function tableName() {
        return "mis.medcards";
    }
} 