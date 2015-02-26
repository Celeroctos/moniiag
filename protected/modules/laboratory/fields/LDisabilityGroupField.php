<?php

class LDisabilityGroupField extends LDropDown {

    /**
     * Override that method to return associative array
     * for drop down list
     * @return array - Array with data
     */
    public function data() {
        return [
            1 => "I",
            2 => "II",
            3 => "III",
            4 => "IV"
        ];
    }

    /**
     * Override that method to return field's key
     * @return String - Key
     */
    public function key() {
        return "DisabilityGroup";
    }

    /**
     * Override that method to return field's label
     * @return String - Label
     */
    public function name() {
        return "Группа инвалидности";
    }
}