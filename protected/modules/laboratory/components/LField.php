<?php

abstract class LField extends CComponent {

	/**
	 * Construct field
	 */
	public function __construct() {
		$this->name = $this->name();
		$this->key = strtolower($this->key());
	}

	/**
	 * Render field with value or data
	 * @param CActiveForm $form - Form
	 * @param LFormModel $model - Model
	 * @param String $label - Field's label
	 * @param Mixed $value - Any value for field
	 * @param Array $data - Array with values (for DropDown lists)
	 * @return String - Just rendered field result
	 */
	public final function renderEx($form, $model, $label = "", $value = null, $data = []) {

		assert(is_string($label), "Label must be with String type");
		assert(is_array($data), "Data must be with Array type");

		$this->label = $label;
		$this->value = $value;
		$this->data = $data;

		return $this->render($form, $model);
	}

	/**
	 * Override that method to render field base on it's type
	 * @param CActiveForm $form - Form
	 * @param LFormModel $model - Model
	 * @return String - Just rendered field result
	 */
	public abstract function render($form, $model);

	/**
	 * Override that method to return field's key
	 * @return String - Key
	 */
	public abstract function key();

	/**
	 * Override that method to return field's label
	 * @return String - Label
	 */
	public abstract function name();

	/**
	 * @return Mixed - Field's value (optional)
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @return String - Field's key name
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * @return String - Field's label associated with key
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return Array - Array with data for DropDown list
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @return String - Field's label
	 */
	public function getLabel() {
		return $this->label;
	}

	private $value;
	private $key;
	private $name;
	private $data;
	private $label;
}