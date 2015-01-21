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
    private function _buildFromConfig($config = null) {

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

        foreach ($config as $key => $field) {

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

            // Dynamically declare empty self's variable
            $this->_container[$key] = null;
        }
    }

    /**
     * Get form model's rules associated with fields names
     * @return Array - Rules for form's mode;
     */
    public final function rules() {
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
    public final function attributeLabels() {
        if ($this->_labels) {
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

    private $_container = [];
    private $_rules = null;
    private $_labels = null;
    private $_types = null;
} 