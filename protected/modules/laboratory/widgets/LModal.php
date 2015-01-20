<?php

class LModal extends LWidget {

    public $title = null;
    public $id = null;
    public $body = null;
    public $buttons = null;

    public function run($return = false) {

        if ($this->body instanceof LWidget) {
            $this->body = $this->body->run(true);
        }

        $this->render(__CLASS__, null, $return);
    }
} 