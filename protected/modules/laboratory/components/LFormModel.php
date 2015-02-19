<?php

abstract class LFormModel extends CFormModel {

    /**
     * Override that method to return config. Config should return array associated with
     * model's variables. Every field must contains 3 parameters:
     *  + label - Variable's label, will be displayed in the form
     *  + type - Input type (@see _LFormInternalRender#render())
     *  + rules - Basic form's Yii rules, such as 'required' or 'numeric' etc
     * @return Array - Model's config
     */
    public abstract function config();

    /**
     * That function will check model's field for valid for drop down list
     * @param string $field - Field's name
     * @return bool - True if field can be used for data receive from db
     */
    public function isActive($field) {
        return isset($this->$field) && $this->$field && $this->$field != -1;
    }

    /**
     * Construct table with configuration build
     * @param array|null $config - Array with model's configuration
     */
    public function __construct($config = null) {
		$this->_buildFromConfig($config);
    }

    /**
     * That method will return declared variable from models
     * @param String $name - Variable name
     * @return mixed|null - Variable value or null
     */
    public function __get($name) {
        if (isset($this->_container[$name])) {
            return $this->_container[$name];
        } else {
            return null;
        }
    }

    /**
     * Assign some value to variable and store it in container
     * @param String $name - Name
     * @param mixed $value - Value
     * @return void
     */
    public function __set($name, $value) {
        $this->_container[$name] = $value;
    }

    /**
     * Build form from models configuration
     * @param array|null $config - Array with model's configuration
     */
    protected function _buildFromConfig($config = null) {

        // If we have rules and labels then skip it
        if ($this->_rules && $this->_labels && $this->_types) {
            return;
        }

        // Get model's configuration
        if ($config == null) {
            $config = $this->config();
        }

        // Reset rules and labels arrays
        $this->_rules = [];
        $this->_labels = [];
        $this->_types = [];

        foreach ($config as $key => &$field) {

            // If programmer forget to get value from model
            if (!isset($field["value"]) && isset($this->$key)) {
                $field["value"] = $this->$key;
            }

            // Assign labels and rules arrays
            if (isset($field["label"])) {
                $this->_labels[$key] = $field["label"];
            } else {
                $this->_labels[$key] = "";
            }
            if (isset($field["rules"])) {
                $rules = explode(",", $field["rules"]);
                foreach ($rules as $i => $rule) {
                    $rule = trim($rule);
                    if ($rule == "required") { // && class_exists("LRequiredValidator")) {
                        $rule = "LRequiredValidator";
                    }
                    if (!isset($this->_rules[$rule])) {
                        $this->_rules[$rule] = [];
                    }
                    array_push($this->_rules[$rule], $key);
                }
            }
            if (isset($field["types"])) {
                $this->_types[$key] = $field["types"];
            } else {
                $this->_types[$key] = "";
            }

            // Dynamically declare empty variable
            $this->_container[$key] = null;
        }
    }

	/**
	 * Get data for key, that method - is result or optimization, when
	 * all data stuff was in basic configuration
	 * @param string $key - Name of unique field's identification number
	 * @return array - Array with data or null, if method hasn't been declared
	 */
	public function getKeyData($key) {
		$method = "get".self::changeNotation($key)."Data";
		if (method_exists($this, $method)) {
			return $this->$method();
		} else {
			return null;
		}
	}

	/**
	 * That function will change variable's notation from database's to
	 * classic PHP without '_' as delimiter. For example, name guide_id will
	 * be converted to GuideId
	 * @param string $name - Name to change
	 * @param bool $startWithUpper - Set that flag to false to set first letter to lower case
	 * @return string - Formatted string
	 */
	public static function changeNotation($name, $startWithUpper = true) {
		$result = "";
		foreach (explode("_", $name) as $word) {
			$result .= strtoupper(substr($word, 0, 1)) . substr($word, 1);
		}
		if (!$startWithUpper) {
			$result[0] = strtolower($result[0]);
		}
		return $result;
	}

    /**
     * Convert array with array models to list data, it will be faster
     * and easier in use then CHtml::listData method
     * @param array $models - Array with models
     * @param string $id - Name of select value
     * @param string $value - Name of select option text
     * @return array - Array with result map
     */
    public static function listData($models, $id, $value) {
        $result = [];
        foreach ($models as &$model) {
            $result[$model[$id]] = $model[$value];
        }
        return $result;
    }

    /**
     * Reset configuration
     */
    protected function reset() {

        // Reset arrays
        $this->_rules = null;
        $this->_labels = null;
        $this->_types = null;

        // Recompute configuration
        $this->_buildFromConfig();
    }

    /**
     * Get form model's rules associated with fields names
     * @return Array - Rules for form's mode;
     */
    public function rules() {
        if (!$this->_rules) {
            $this->_buildFromConfig();
        }
        $result = [];
        foreach ($this->_rules as $rule => $rules) {
            array_push($result, [
                implode(", ", $rules), $rule
            ]);
        }
        return $result;
    }

    /**
     * Get form model's labels
     * @return Array - Array with labels associated with fields names
     */
    public function attributeLabels() {
        if (!$this->_labels) {
            $this->_buildFromConfig();
        }
        return $this->_labels;
    }

    /**
     * @return Array - Array with declared variables
     */
    public function getContainer() {
        return $this->_container;
    }

    protected $_container = [];
    protected $_rules = null;
    protected $_labels = null;
    protected $_types = null;

} 