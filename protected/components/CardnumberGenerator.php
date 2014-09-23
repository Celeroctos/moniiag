<?php
class CardnumberGenerator extends CComponent {
	private $prevNumber = null;
	public function generateNumber($ruleId) {
		$rule = MedcardRule::model()->findByPk($ruleId);
		if($rule == null) {
			return null;
		}

		$number = $this->generate($this->generatePrefix($rule), $this->generatePostfix($rule), $rule);
		var_dump($number);
		return $number;
	}
	
	public function setPrevNumber($number) {
		$this->prevNumber = $number;
	}
	
	private function generatePrefix($rule) {
		if($rule->prefix_id == -2) { // ГГ
			return date('y').'/';
		} elseif($rule->prefix_id == -3) { // ГГГГ
			return date('Y').'/';
		}  elseif($rule->prefix_id > 0) { // Не "не имеется"
			$prefix = MedcardPrefix::model()->findByPk($rule->prefix_id);
			return $prefix->value;
		}
		return '';
	}
	
	private function generatePostfix($rule) {
		if($rule->postfix_id == -2) { // ГГ
			return '/'.date('y');
		} elseif($rule->postfix_id == -3) { // ГГГГ
			return '/'.date('Y');
		} elseif($rule->postfix_id > 0) { // Не "не имеется"
			$postfix = MedcardPostfix::model()->findByPk($rule->postfix_id);
			return $postfix->value;
		}
		return '';
	}
	
	private function generate($prefix, $postfix, $rule) {
		if($rule->prefix_id == -2) {
			$year = mb_substr($prefix, 0, mb_strlen($prefix) - 1);
		} elseif($rule->prefix_id == -3) {
			$year = mb_substr($prefix, 2, 2);
		} elseif($rule->postfix_id == -2) {
			$year = mb_substr($postfix, 1, 2);
		} elseif($rule->postfix_id == -3) {
			$year = mb_substr($postfix, 3);
		} else {
			$year = date('y');
		}

		if($rule->value == 0) { // Каждый год с первого номера
			$medcard = MedcardHistory::model()->getLastNumberByYear($year, $rule->id);
			// Чтобы узнать сам номер: если год - это префикс, режем с конца. Если постфикс - с начала
			if($medcard == null) {
				$number = 0; // Это для того, чтобы do-while сгенерировал 1чку
			} else {
				$number = $this->getNumber($rule->prefix_id, $rule->postfix_id, $medcard['to']);
			}
		} elseif($rule->value == 1) { // Сквозная
			$medcard = MedcardHistory::model()->getLastNumberThrough();
			// Чтобы узнать сам номер: если год - это префикс, режем с конца. Если постфикс - с начала
			if($medcard == null) {
				$number = 0; // Это для того, чтобы do-while сгенерировал 1чку
			} else {
				$number = $this->getNumber($rule->prefix_id, $rule->postfix_id, $medcard['to']);
			}
		} elseif($rule->value == 2) { // На основе существующего номера
			if($this->prevNumber != null) {
				if($rule->participle_mode == 0) { // Режим добавления второго префикса и постфикса
				
				}
				if($rule->participle_mode == 1) { // Режим замены
				
				}
			}
		}

		if($number === null) { // Это обычный префикс и постфикс, режем от них
			$prefixModel = MedcardPrefix::model()->findByPk($rule->prefix_id);
			$postfixModel = MedcardPostfix::model()->findByPk($rule->postfix_id);
			$preNumber = mb_substr($medcard['to'], mb_strpos($medcard['to'], $prefixModel->value) + mb_strlen($prefixModel->value));
			$numberWithSlash = mb_substr($preNumber, 0, mb_strpos($preNumber, $postfixModel->value));
			$number = explode('/', $numberWithSlash)[0];
		}

		// Генерируем номер: обрабатываем коллизии на всякий случай, если номера идут не по порядку
		$flag = true;
		do {
			$str = '';
			++$number;
			if($prefix != null) {
				$str = $prefix;
			}
			$str .= $number;
			if($postfix != null) {
				$str .= $postfix;
			}

			$issetMedcard = MedcardHistory::model()->find('t.to = :to AND enterprise_id = :enterprise_id', array(':to' => $str, ':enterprise_id' => 1)); // TODO : привязать заведение
			if(!$issetMedcard) {
				$flag = false;
			}
		} while($flag);
		return $str;
	}
	
	private function getNumber($prefix_id, $postfix_id, $data) {
		if($prefix_id == -2 || $prefix_id == -3) {
			$number = mb_substr($data, strpos($data, '/')); 
		} elseif($prefix_id != null) {
			$prefixModel = MedcardPrefix::model()->findByPk($prefix_id);
			$number = mb_substr($data, mb_strpos($data, $prefixModel->value) + mb_strlen($prefixModel->value));
		}

		if($postfix_id == -2 || $postfix_id == -3) {
			$number = mb_substr($data, 0, strpos($data, '/')); 
		} elseif($postfix_id != null) {
			$postfixModel = MedcardPostfix::model()->findByPk($postfix_id);
			$number = mb_substr($data, mb_strpos($data, $postfixModel->value) + mb_strlen($postfixModel->value));
		}	

		return $number;
	}
}

?>