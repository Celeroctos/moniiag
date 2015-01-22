<?php

class LWidget extends CWidget {

    /**
     * Override that method to return
     * @param bool $return - If true, then widget shall return rendered component
     * else it should print to output stream
     * @return string - Just rendered component or nothing
     */
    public function run($return = false) {
        return $this->render(__CLASS__);
    }

    /**
     * Try to get default value for some field
     * @param string $key - Value's key
     * @return mixed - Default value or null
     */
    public function getDefault($key) {
        if (isset($this->_model[$key])) {
            return $this->_model[$key];
        }
        return null;
    }

    /**
     * @param array $model - Array with form's default values
     */
    public function setModel($model) {
        $this->_model = $model;
    }

    /**
     * @return array - Array with form's default values
     */
    public function getModel() {
        return $this->_model;
    }

    private $_model = null;
} 