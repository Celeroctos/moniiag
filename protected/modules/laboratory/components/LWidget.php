<?php

class LWidget extends CWidget {

    /**
     * Override that method to return just rendered component
     * @param bool $return - If true, then widget shall return rendered component else it should print to output stream
     * @return string - Just rendered component or nothing
     */
    public function run($return = false) {
        return $this->render(__CLASS__, null, $return);
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
     * Render widget and return it's just rendered component
     * @param string $class - Path to widget to render
     * @param array $properties - Widget's properties
     * @return mixed|void
     */
    public function getWidget($class, $properties = []) {
        return $this->widget($class, $properties, true);
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