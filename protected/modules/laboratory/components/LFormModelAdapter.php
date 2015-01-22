<?php

class LFormModelAdapter extends LFormModel {

    /**
     * @param array|null $config - Model configuration
     */
    public function __construct($config = null) {
        parent::__construct($config);
        $this->_config = $config;
    }

    /**
     * Put some value to configuration table
     * @param string $key - Element's key
     * @param mixed $value - Value to put
     */
    public function putValue($key, $value) {
        $this->_config[$key]["value"] = $value;
    }

    /**
     * Reset configuration
     */
    public function reset() {

        // Reset arrays
        $this->_rules = null;
        $this->_labels = null;
        $this->_types = null;

        // Rebuild configuration
        $this->_buildFromConfig($this->_config);
    }

    /**
     * Override that method to return config. Config should return array associated with
     * model's variables. Every field must contains 3 parameters:
     *  + label - Variable's label, will be displayed in the form
     *  + type - Input type (@see LForm::renderField())
     *  + rules - Basic form's Yii rules, such as 'required' or 'numeric' etc
     * @return Array - Model's config
     */
    public function config() {
        return $this->_config;
    }

    private $_config = null;
}