<?php

class GuideValidator extends CValidator {

	/**
	 * Validates a single attribute.
	 * This method should be overridden by child classes.
	 * @param mixed $object the data object being validated
	 * @param string $attribute the name of the attribute to be validated.
	 */
	protected function validateAttribute($object, $attribute) {

		if ($object->type != 2 &&
			$object->type != 3 &&
			$object->type != 7
		) {
			return ;
		}

		$this->_value = $object->$attribute;
		$this->_object = $object;
		$this->_attribute = $attribute;

		if (!isset($object->guideId) || $object->guideId == -1) {
			$this->error();
		}
	}

	/**
	 * Set error message to validator
	 */
	private function error() {
		$message = $this->message !== null ? $this->message :
			"Необходимо заполнить поле «{attribute}»";
		$this->addError($this->_object, $this->_attribute, $message);
	}

	/**
	 * @var mixed
	 */
	private $_object = null;
	private $_attribute = null;
	private $_value = null;
}