<?php

class LMenstruationField extends LDropDown {

	/**
	 * Override that method to return associative array
	 * for drop down list
	 * @return array - Array with data
	 */
	public function data() {
		return [
			0 => "Фолликулярная фаза",
			1 => "Овуляторная фаза",
			2 => "Лютеиновая фаза",
			3 => "Постменопауза"
		];
	}

	/**
	 * Override that method to return field's key
	 * @return String - Key
	 */
	public function key() {
		return "Menstruation";
	}

	/**
	 * Override that method to return field's label
	 * @return String - Label
	 */
	public function name() {
		return "Фаза менструации";
	}
}