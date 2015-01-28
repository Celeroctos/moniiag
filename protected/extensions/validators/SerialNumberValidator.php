<?php
class SerialNumberValidator extends CValidator
{
	public $pattern='/^[a-zA-Z0-9а-яA-Я\s\-#№]+$/';
    protected function validateAttribute($object,$attribute) {
		$value = $object->$attribute;
		if(!$this->validateValue($value) || $this->isEmpty($value)) {
			$this->addError($object,$attribute,Yii::t('yii','Недопустимый символ в поле {attribute}, либо поле пусто.'));
            return false;
		}
	} 
	
	public function validateValue($value)
	{
		return preg_match($this->pattern,$value);
	}
}
?>