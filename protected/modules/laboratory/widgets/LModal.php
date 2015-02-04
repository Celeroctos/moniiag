<?php

class LModal extends LWidget {

    public $title = null;
    public $id = null;
    public $body = null;
    public $buttons = [];

    public function run() {
        if ($this->body instanceof LWidget) {
            $this->body = $this->body->call(true);
        }
        foreach ($this->buttons as $i => &$button) {
            if (!isset($button["type"])) {
                $button["type"] = "button";
            }
            if (!isset($button["attributes"]) || !is_array($button["attributes"])) {
                continue;
            }
            $attribute = "";
            foreach ($button["attributes"] as $key => $value) {
                $attribute .= $key."=\"{$value}\" ";
            }
            $button["attributes"] = $attribute;
        }
        $this->render(__CLASS__, null);
    }
} 