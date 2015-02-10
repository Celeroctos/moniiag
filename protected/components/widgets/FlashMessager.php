<?php
/**
 * Виджет для работы с Flash (см. методы getFlash/setFlash yii)
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
class FlashMessager extends CWidget
{
	public $messageTypes=['info', 'success', 'warning', 'danger', 'error'];
	public $classPrefix='alert alert-'; //twitter bootstrap-3 style
	
	public function run()
	{
		foreach ($this->messageTypes as $messageType)
		{
			if (Yii::app()->user->hasFlash($messageType))
			{
				$flash=Yii::app()->user->getFlash($messageType, null, true);
				
				if(is_array($flash))
				{
					$messages=$flash;
				}
				else
				{
					$messages=array($flash);
				}
				end($messages);
				
				while($message = current($messages))
				{
					echo '<div class="'.$this->classPrefix.$messageType.'">'.$message.'</div>';
					prev($messages);
				}
			}
		}
	}
}