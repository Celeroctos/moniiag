<?php
/**
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
class WebUser extends CWebUser
{
	const MSG_SUCCESS = 'success';
	const MSG_ERROR = 'error';
	const MSG_INFO = 'info';
	const MSG_WARNING = 'warning';
	
	public function getFlashMessageKey($messageType)
	{
		switch ($messageType) {
			case self::MSG_SUCCESS:
				$key = 'success';
				break;
			case self::MSG_ERROR:
				$key = 'danger';
				break;
			case self::MSG_INFO:
				$key = 'info';
				break;
			case self::MSG_WARNING:
				$key = 'warning';
			default:
				$key = 'info';
				break;
		}
		return $key;
	}

	public function addFlashMessage($messageType, $message)
	{
		$key=$this->getFlashMessageKey($messageType);

		if ($this->hasFlash($key))
		{
			$messages=$this->getFlash($key);
		}
		else
		{
			$messages=[];
		}

		$messages[]=$message;
		$this->setFlash($key, $messages);
	}

	public function clearFlashMessages($messageType = null)
	{
		if(!isset($messageType)) 
		{
			foreach ([self::MSG_SUCCESS, self::MSG_ERROR, self::MSG_INFO] as $messageType) 
			{
				$this->clearFlashMessages($messageType);
			}
			return;
		}

		$key=$this->getFlashMessageKey($messageType);
		$this->setFlash($key, []);
	}
}