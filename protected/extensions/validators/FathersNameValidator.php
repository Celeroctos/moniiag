<?php
class FathersNameValidator extends CValidator
{
		public $pattern='/[A-Я]([a-я])+(\x20[A-Яа-я]([a-я])+)*/';
		// Фамилия может  состоять из нескольких слов, длина
		//    которых не меньше двух символов, первое из 
		// которых должно начинаться с большой буквы, слова разделены пробелами
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
				Yii::t('yii','{attribute}')
			);
		}
	}
	
	public function validateValue($value)
	{
		return preg_match($this->pattern,$value);
	}
	
}
?>