<?php
class PassportValidator extends CValidator
{
	public $pattern='/^[a-zA-Z0-9а-яA-Я\s]+$/';

	protected function validateAttribute($object,$attribute) {
		$value = $object->$attribute;
		if($this->isEmpty($value)) {
			return false;		
		}

		if(!$this->validateValue($value)){
			$this->addError($object,$attribute,Yii::t('yii', 'Недопустимый символ в поле {attribute}'));
			return false;
		}

		return true;
	} 
	
	public function validateValue($value) {
		return preg_match($this->pattern,$value);
	}
}
?>