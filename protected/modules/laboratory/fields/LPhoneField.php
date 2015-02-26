<?php

class LPhoneField extends LField {

	/**
	 * Override that method to render field base on it's type
	 * @param CActiveForm $form - Form
	 * @param LFormModel $model - Model
	 * @return String - Just rendered field result
	 */
	public function render($form, $model) {
		return $form->textField($model, $this->getLabel(), [
			'placeholder' => '+7 (000) 000 00 00',
            'data-regexp' => '^\\+\\s*[1-9]\\s*\\([0-9\\s]{3}\s\\)\\s*[0-9]{3}\\s[0-9]{2}\\s[0-9]{2}\\s',
			'id' => $this->getKey(),
			'class' => 'form-control',
			'value' => $this->getValue()
		] + $this->getOptions());
	}

	/**
	 * Override that method to return field's key
	 * @return String - Key
	 */
	public function key() {
		return "Phone";
	}

	/**
	 * Override that method to return field's label
	 * @return String - Label
	 */
	public function name() {
		return "Телефон";
	}
}