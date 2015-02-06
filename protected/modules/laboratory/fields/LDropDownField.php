<?php

class LDropDownField extends LField {

	/**
	 * Override that method to render field base on it's type
	 * @param CActiveForm $form - Form
	 * @param LFormModel $model - Model
	 * @return String - Just rendered field result
	 */
	public function render($form, $model) {
		$data = $this->getData();
		if (!isset($data[-1]) && !$this->getValue()) {
			$data = [ -1 => "Нет" ] + $data;
		}
		if (!count($data)) {
			$data = [ -1 => "Нет" ];
		}
		return $form->dropDownList($model, $this->getKey(), $data, [
			'placeholder' => $this->getLabel(),
			'id' => $this->getKey(),
			'class' => 'form-control',
			'value' => $this->getValue(),
			'onchange' => "DropDown && DropDown.change && DropDown.change.call(this)",
			'options' => [ $this->getValue() => [ 'selected' => true ] ]
		]);
	}

	/**
	 * Override that method to return field's key
	 * @return String - Key
	 */
	public function key() {
		return "DropDown";
	}

	/**
	 * Override that method to return field's label
	 * @return String - Label
	 */
	public function name() {
		return "Выпадающий список";
	}
}