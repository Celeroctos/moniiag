<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2015-02-18
 * Time: 11:40
 */

class LPatientInfo extends LWidget {

    public function run() {
        $this->render(__CLASS__, [
            "patient" => new LPatient()
        ]);
    }
} 