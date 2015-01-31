<?php

class LTextAreaField extends LField {

	/**
	 * Override that method to render field base on it's type
	 * @param CActiveForm $form - Form
	 * @param LFormModel $model - Model
	 * @return String - Just rendered field result
	 */
	public function render($form, $model) {
		return $form->textArea($model, $this->getKey(), [
			'placeholder' => $this->getLabel(),
			'id' => $this->getKey(),
			'class' => 'form-control',
			'value' => $this->getValue(),
			'style' => 'resize: vertical'
		]);
	}

	/**
	 * Override that method to return field's key
	 * @return String - Key
	 */
	public function key() {
		return "TextArea";
	}

	/**
	 * Override that method to return field's label
	 * @return String - Label
	 */
	public function name() {
		return "Текстовая область";
	}
}