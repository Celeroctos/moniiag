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
} 