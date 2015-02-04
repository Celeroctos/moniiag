<?php

class LPanel extends LWidget {

    public $title = null;
    public $id = null;
    public $body = null;

    public function init() {
        parent::init();
        if ($this->body instanceof LWidget) {
            $this->body = $this->body->call();
        }
        $this->render(__CLASS__, [], false);
    }

    public function run() {
        print ob_get_clean().CHtml::closeTag("div").CHtml::closeTag("div");
    }
} 