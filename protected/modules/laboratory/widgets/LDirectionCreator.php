<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2015-02-18
 * Time: 11:15
 */

class LDirectionCreator extends LWidget {

    public $id = "direction-creator-form";

    public function run() {
        $this->render(__CLASS__, [
            "model" => new LDirectionForm()
        ]);
    }
} 