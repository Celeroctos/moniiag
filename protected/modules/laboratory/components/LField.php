<?php

abstract class LField extends CComponent {

	/**
	 * Construct field
	 */
	public function __construct() {
		$this->name = $this->name();
		$this->type = strtolower(
            $this->key()
        );
	}

    /**
     * Override that method to get current field instance
     * @param string $class - Name of field's class
     * @return LField - Field object
     */
    public static function field($class = __CLASS__) {
        if (!isset(self::$_cached[$class])) {
            return (self::$_cached[$class] = new $class());
        } else {
            return self::$_cached[$class];
        }
    }

    private static $_cached = [];

	/**
	 * Render field with value or data
	 * @param CActiveForm $form - Active form for which we're rendering fields
	 * @param LFormModel $model - Form's model with data configuration
	 * @param String $key - Unique key for field (identification value)
	 * @param String $label - Field's label
	 * @param Mixed $value - Any value for field
	 * @param Array $data - Array with values (for DropDown lists)
	 * @param array $options - Html options for field component
	 * @return String - Just rendered field result
	 */
	public final function renderEx($form, $model, $key, $label = "", $value = null, $data = [], $options = []) {

		assert(is_string($label), "Label must be with String type");
		assert(is_string($key), "Key must be with String type");
		assert(is_array($data), "Data must be with Array type");
		assert(is_array($options), "Options must be with Array type");

		$this->label = $label;
		$this->key = $key;
		$this->value = $value;
		$this->data = $data;
		$this->options = $options;

		$this->options += [
			"id" => $key,
			"placeholder" => $label
		];

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

	/**
	 * @return String - Field's type (unique key)
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return Array - Array with html options
	 */
	public function getOptions() {
		return $this->options;
	}

	private $value;
	private $key;
	private $type;
	private $name;
	private $data;
	private $label;
	private $options;
}