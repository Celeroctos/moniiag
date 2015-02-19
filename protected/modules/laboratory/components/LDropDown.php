<?php

abstract class LDropDown extends LField {

	/**
	 * Override that method to render field base on it's type
	 * @param CActiveForm $form - Form
	 * @param LFormModel $model - Model
	 * @return String - Just rendered field result
	 */
	public final function render($form, $model) {
		$data = $this->data();
		if (!$this->isBoolean() && !isset($data[-1])) {
			$data = [ -1 => "Нет" ] + $data;
		}
		return $form->dropDownList($model, $this->getKey(), $data, [
			'placeholder' => $this->getLabel(),
			'id' => $this->getKey(),
			'class' => 'form-control',
			'options' => [ $this->getValue() => [ 'selected' => true ] ]
		]);
	}

	/**
	 * Override that method to make that field as boolean type
	 * @return bool - True, if your subtype is boolean like
	 */
	public function isBoolean() {
		return false;
	}

	/**
	 * Override that method to return associative array
	 * for drop down list
	 * @return array - Array with data
	 */
	public abstract function data();
}