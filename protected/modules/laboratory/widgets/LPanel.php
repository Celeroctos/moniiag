<?php

class LPanel extends LWidget {

    public $title = null;
    public $id = null;
    public $body = null;
    public $collapse = null;

    public function init() {
        parent::init();
        if ($this->body instanceof LWidget && !empty($this->body)) {
            $this->body = $this->body->call();
        }
		if (empty($this->id)) {
			$this->id = bin2hex(openssl_random_pseudo_bytes(5));
		}
		ob_start();
        $this->render(__CLASS__, [], false);
    }

    public function run() {
        print ob_get_clean().CHtml::closeTag("div").CHtml::closeTag("div");
    }
} 