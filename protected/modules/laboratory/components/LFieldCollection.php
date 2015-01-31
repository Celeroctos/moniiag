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
		if (isset($this->fields[$field->getType()])) {
			throw new CException("Field with that key already registered in collection ({$field->getType()})");
		}
		$this->fields[$field->getType()] = $field;
		$this->select[$field->getType()] = $field->getName();
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
			throw new CException("Not implemented field ({$key}) ");
		}
		return $this->fields[$key];
	}

	/**
	 * Get array with prepared array for drop down list
	 * @return Array - Array with keys and it's associated labels
	 */
	public function getDropDown() {
		return $this->select;
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
	 * Don't construct that class
	 */
	private function __construct() {
		$declared = [
			new LDateField(),
			new LDropDownField(),
			new LFileField(),
			new LHiddenField(),
			new LNumberField(),
			new LPasswordField(),
			new LRadioField(),
			new LTextAreaField(),
			new LTextField(),
			new LYesNoField()
		];
		foreach ($declared as $field) {
			$this->add($field);
		}
	}

	private static $collection = null;
}