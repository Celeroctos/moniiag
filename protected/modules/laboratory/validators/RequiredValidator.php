<?php

class RequiredValidator extends CRequiredValidator {

	/**
	 * Validates a single attribute.
	 * This method should be overridden by child classes.
	 * @param mixed|LFormModel $object the data object being validated
	 * @param string $attribute the name of the attribute to be validated.
	 */
	protected function validateAttribute($object, $attribute) {
		if ($object instanceof LFormModel) {
			$config = $object->config();
			if (isset($config[$attribute]) && isset($config[$attribute]["type"])) {
				if ($object->$attribute == "-1") {
					$this->error($object, $attribute);
				}
			} else {
				$this->error($object, $attribute);
			}
		} else {
			parent::validateAttribute($object, $attribute);
		}
	}

	/**
	 * Add error message for current validation loop
	 * @param mixed|LFormModel $object the data object being validated
	 * @param string $attribute the name of the attribute to be validated.
	 */
	protected function error($object, $attribute) {
		$this->addError($object, $attribute, Yii::t("yii", "{attribute} must be {value}.", [
				"{value}" => $this->requiredValue
			]
		));
	}
}