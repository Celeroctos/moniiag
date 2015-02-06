<?php

class LFieldCollection extends CComponent {

	/**
	 * Get collection's singleton instance
	 * @return LFieldCollection - Field collection instance
	 */
	public static function getCollection() {
		if (!self::$collection) {
			self::$collection = new LFieldCollection();
		}
		return self::$collection;
	}

	/**
	 * Insert field into collection
	 * @param LField $field - Field to register
	 * @throws CException
	 */
	public function add($field) {
		$key = strtolower($field->getType());
		if (isset($this->fields[$key])) {
			throw new CException("Field with that key already registered in collection ({$field->getType()})");
		}
		$this->fields[$key] = $field;
		$this->select[$key] = $field->getName();
	}

	/**
	 * Find field by it's key in collection
	 * @param String $key - Field's key
	 * @return LField - Field instance
	 * @throws CException
	 */
	public function find($key) {
		$key = strtolower($key);
		if (!isset($this->fields[$key])) {
			throw new CException("Unresolved or not implemented field type ({$key})");
		}
		return $this->fields[$key];
	}

	/**
	 * Get array with prepared array for drop down list
	 * @param array $allowed - Array with allowed types
	 * @return array - Array with keys and it's associated labels
	 */
	public function getDropDown(array $allowed = null) {
		if ($allowed == null) {
			return $this->select;
		}
		$array = [];
		foreach ($allowed as $i => $value) {
			$array[strtolower($value)] = $this->select[strtolower($value)];
		}
		return $array;
	}

	/**
	 * Get array with all registered fields
	 * @return Array - Array with registered field's instances
	 */
	public function getList() {
		return $this->fields;
	}

	private $fields = [];
	private $select = [];

	/**
	 * Don't construct that class by yourself
	 */
	private function __construct() {
		$handle = opendir("protected/modules/laboratory/fields");
		if ($handle === false) {
			throw new CException("Can't read field with fields");
		}
		while (($entry = readdir($handle)) !== false) {
			if ($entry != "." && $entry != "..") {
				$entry = basename($entry, ".php");
				$this->add(new $entry());
			}
		}
		closedir($handle);
	}

	private static $collection = null;
}