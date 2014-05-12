<?php
class SerialNumberValidator extends CValidator
{
	public $pattern='/^[a-zA-Z0-9а-яA-Я]+$/';
	// Разрешены цифры, латинский и русские буквы
		
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

			
			//$message=Yii::t('yii','Недопустимый символ в поле {attribute}');
			//echo(Yii::t('yii','Недопустимый символ в поле {attribute}'));
			$this->addError($object,$attribute,Yii::t('yii',
			/*'Недопустимый символ в поле {attribute}')*/
			'Недопустимый символ в поле {attribute}')
			);
		}
	} 
	
	public function validateValue($value)
	{
		return preg_match($this->pattern,$value);
	}
}
?>