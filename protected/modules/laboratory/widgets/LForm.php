<?php

class LForm extends LWidget {

    public $title = null;

    public function run($return = false) {
        $this->render(__CLASS__, null, $return);
    }
} 