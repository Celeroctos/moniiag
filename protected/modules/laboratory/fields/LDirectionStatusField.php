<?php

class LDirectionStatusField extends LDropDown {

    public function isBoolean() {
		return true;
	}

	/**
	 * Override that method to return associative array
	 * for drop down list
	 * @return array - Array with data
	 */
	public function data() {
		return [
			0 => "Новое",
			1 => "Произведен забор образца",
			2 => "Проведён анализ"
		];
	}

	/**
	 * Override that method to return field's key
	 * @return String - Key
	 */
	public function key() {
		return "DirectionStatus";
	}

	/**
	 * Override that method to return field's label
	 * @return String - Label
	 */
	public function name() {
		return "Статус направления";
	}
}