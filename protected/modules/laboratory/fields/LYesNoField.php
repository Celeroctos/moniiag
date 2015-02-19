<?php

class LYesNoField extends LDropDown {

    /**
     * Override that method to return associative array
     * for drop down list
     * @return array - Array with data
     */
    public function data() {
        return [
            0 => "Нет",
            1 => "Да"
        ];
    }

	/**
	 * Override that method to return field's key
	 * @return String - Key
	 */
	public function key() {
		return "YesNo";
	}

	/**
	 * Override that method to return field's label
	 * @return String - Label
	 */
	public function name() {
		return "Логический";
	}
}