<?php
class SNILSValidator extends CValidator
{
	public $pattern='/^([0-9]{3}\-){2}[0-9]{3}(\x20|-)[0-9]{2}$/';
	// Формат ddd-ddd-ddd dd
	
	protected function validateAttribute($object,$attribute)
	{
		// Проверяем - если значение пустое, то не валидируем
		//       если значение пустое, но нельзя чтобы так было, то на это должен среагировать 
		//  только валидатор пустого ввода
		$value=$object->$attribute;
		if($this->isEmpty($value))
		{
			return;		
		}
		
		// Проверяем формат и в случае если не правильный - выводим сообщение об ошибке
		if(!$this->validateValue($value))
		{
			$this->addError($object,$attribute,
				Yii::t('yii','Неправильно введён {attribute}')
			);
		}
	}
	
	public function validateValue($value)
	{
		return preg_match($this->pattern,$value);
	}
	
	
}
?>