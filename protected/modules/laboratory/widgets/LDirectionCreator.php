<?php

class LDirectionCreator extends LWidget {

    public $id = "direction-creator-form";

    public function run() {
        $this->render(__CLASS__, [
            "model" => new LDirectionForm()
        ]);
    }
} 