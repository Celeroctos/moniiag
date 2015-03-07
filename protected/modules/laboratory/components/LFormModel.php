<?php

abstract class LFormModel extends CFormModel {

	/**
	 * Override that variable to return list default
	 * validators aliases and it's replacement
	 * @var array - Array with replacements
	 * @see replace
	 */
	protected $replace = [
		"required" => "LRequiredValidator",
		"hidden" => "LHiddenValidator"
	];

	/**
	 * Array with configuration (for future optimization)
	 * @var array - Array with configuration
	 * @deprecated - Unused
	 */
	protected $config = [];

	/**
	 * Constructor.
	 * @param string $scenario name of the scenario that this model is used in.
	 * See {@link CModel::scenario} on how scenario is used by models.
	 * @see getScenario
	 */
	public function __construct($scenario = "") {
		parent::__construct($scenario);
	}

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
	 * Override that method to return list with replacements, don't
	 * forget parents replacements
	 * @return array - Array with rules replacements
	 */
	public function replace() {
		return $this->replace;
	}

	/**
	 * Override that method to return additional rule configuration, like
	 * scenario conditions or others
	 * @return array - Array with rule configuration
	 */
	public function backward() {
		return [
			[ "id", "required", "on" => [ "update", "search" ] ]
		];
	}

    /**
     * That function will check model's field for valid for drop down list
     * @param string $field - Field's name
     * @return bool - True if field can be used for data receive from db
     */
    public function isActive($field) {
        return isset($this->$field) && $this->$field && $this->$field != -1;
    }

	/**
	 * Check is field looks like drop down list
	 * @param $field - Name of field to test
	 * @return bool - True if field is drop down list
	 */
	public function isDropDown($field) {
		if (!$this->_config) {
			$this->_config = $this->config();
		}
		if (!isset($this->_config[$field]) || (!isset($this->_config[$field]["type"]))) {
			return false;
		}
		$type = strtolower($this->_config[$field]["type"]);
		return $type == "dropdown" || $type == "multiple";
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
    private function buildConfig($config = null) {

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
		$this->_strong = [];

        foreach ($config as $key => &$field) {

            // Assign labels and rules arrays
            if (isset($field["label"])) {
                $this->_labels[$key] = $field["label"];
            } else {
                $this->_labels[$key] = "";
            }
            if (isset($field["rules"])) {
				$this->_rules = $this->buildRules($field["rules"], $key);
            }
            if (isset($field["types"])) {
                $this->_types[$key] = $field["types"];
            } else {
                $this->_types[$key] = "";
            }

            // Dynamically declare empty variable
			if (isset($field["value"])) {
				$this->_container[$key] = $field["value"];
			} else {
				$this->_container[$key] = null;
			}
        }
    }

	/**
	 * Find and replace validation rule for fields configuration
	 * @param string|array $rule - Rule options
	 * @return array|string - Sent rule options
	 */
	private function replaceRules(&$rule) {
		if (is_string($rule)) {
			foreach ($this->replace as $old => $new) {
				$rule = str_replace($old, $new, $rule);
			}
			$result = [];
			foreach (explode(",", $rule) as $r) {
				$result[] = trim($r);
			}
			$rule = implode(",", $result);
		} else if (is_array($rule)) {
			foreach ($rule as &$r) {
				$this->replaceRules($r);
			}
		}
		return $rule;
	}

	/**
	 * Build rules array for CFormModel
	 * @param string|array $rules - Array with rules or simple string with imploded by comma rules
	 * @param string $key - Name of rules key
	 * @return array - Array with built rules
	 */
	private function buildRules($rules, $key) {
		$container = $this->_rules;
		if (is_string($rules)) {
			foreach (explode(",", $rules) as $i => $rule) {
				$rule = trim($rule);
				$this->replaceRules($rule);
				if (!isset($container[$rule])) {
					$container[$rule] = [];
				}
				array_push($container[$rule], $key);
			}
		} else if (is_array($rules)) {
			$this->_strong[] = array_merge([$key], $this->replaceRules($rules));
		}
		return $container;
	}

	/**
	 * Get data for key, that method - is result or optimization, when
	 * all data stuff was in basic configuration
	 * @param string $key - Name of unique field's identification number
	 * @return array - Array with data or null, if method hasn't been declared
	 */
	public function getKeyData($key) {
		$method = "get".self::changeNotation($key);
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
	 * @see CHtml::listData
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
    private function reset() {

        // Reset arrays
        $this->_rules = null;
        $this->_labels = null;
        $this->_types = null;
		$this->_strong = null;

        // Recompute configuration
        $this->buildConfig();
    }

    /**
     * Get form model's rules associated with fields names
     * @return Array - Rules for form's mode;
     */
    public function rules() {
        if (!$this->_rules) {
            $this->buildConfig();
        }
        $result = [];
        foreach ($this->_rules as $rule => $rules) {
			if (is_string($rules)) {
				array_push($result, [
					implode(", ", $rules), $rule
				]);
			} else {
				array_push($result, [
					$rules, $rule
				]);
			}
        }
		return array_merge($result, array_merge($this->_strong,
			$this->replaceRules($this->backward())
		));
    }

    /**
     * Get form model's labels
     * @return Array - Array with labels associated with fields names
     */
    public function attributeLabels() {
        if (!$this->_labels) {
            $this->buildConfig();
        }
        return $this->_labels;
    }

    /**
     * @return Array - Array with declared variables
     */
    public function getContainer() {
		if (!$this->_rules) {
			$this->buildConfig();
		}
        return $this->_container;
    }

    protected $_container = [];
    protected $_rules = null;
	protected $_strong = null;
    protected $_labels = null;
    protected $_types = null;
	protected $_config = null;
    protected $_data = null;
}