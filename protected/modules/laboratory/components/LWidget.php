<?php

class LWidget extends CWidget {

    /**
     * Override that method to update component
     * @param array $properties - Array with properties to update
     */
    public function update($properties) {
        foreach ($properties as $key => $var) {
            $this->$key = $var;
        }
        if (Yii::app()->getRequest()->getIsAjaxRequest()) {
            die(json_encode([
                "component" => $this->run(true),
                "status" => true
            ]));
        } else {
            $this->run(false);
        }
    }

    /**
     * Override that method to return
     * @param bool $return - If true, then widget shall return rendered component
     * else it should print to output stream
     * @return string|void - Just rendered component or nothing
     */
    public function run($return = false) {
        return "";
    }
} 