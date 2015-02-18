<?php

class LPhoneValidator extends CValidator {

	private static $pattern = "/^\\+[1-9]{1}-{0,1}[0-9]{3}-{0,1}[0-9]{3}-{0,1}[0-9]{2}-{0,1}[0-9]{2}$/";

	/**
	 * Validates a single attribute.
	 * This method should be overridden by child classes.
	 * @param LFormModel $object the data object being validated
	 * @param string $attribute the name of the attribute to be validated.
	 */
	protected function validateAttribute($object, $attribute) {
		if (!$this->isEmpty($object->$attribute) && !preg_match(self::$pattern, "{$object->$attribute}")) {
			$this->addError($object, $attribute, "\"{attribute}\" не является телефонным номером");
		}
	}
}