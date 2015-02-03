<?php

class LMultipleField extends LField {

	/**
	 * Override that method to render field base on it's type
	 * @param CActiveForm $form - Form
	 * @param LFormModel $model - Model
	 * @return String - Just rendered field result
	 */
	public function render($form, $model) {
		$multiple = true;
		$data = $this->getData();
		if (!$multiple) {
			if (!isset($data[-1]) && !$this->getValue()) {
				$data = [ -1 => "Нет" ] + $data;
			}
		}
		$content = CHtml::openTag("div", [
			"class" => "multiple"
		]);
		$content .=  $form->dropDownList($model, $this->getKey(), $data, [
			'placeholder' => $this->getLabel(),
			'id' => $this->getKey(),
			'class' => 'form-control multiple-value',
			'value' => $this->getValue(),
			'options' => [ $this->getValue() => [ 'selected' => true ] ],
			'multiple' => $multiple
		]);
		$content .= CHtml::tag("div", [
			"class" => "multiple-container form-control"
		], "", true);
		return $content.CHtml::closeTag("div");
	}

	/**
	 * Override that method to return field's key
	 * @return String - Key
	 */
	public function key() {
		return "Multiple";
	}

	/**
	 * Override that method to return field's label
	 * @return String - Label
	 */
	public function name() {
		return "Множественный выбор";
	}
}