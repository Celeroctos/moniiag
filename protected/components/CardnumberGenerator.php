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
		if($rule->value == 2 && $this->prevNumber != null && $rule->participle_mode_prefix == 1) {
			$prefix = MedcardPostfix::model()->findByPk($rule->prefix_id);
			return $prefix->value;
		}
	
		if($rule->prefix_id == -2) { // ГГ
			return date('y').'/';
		} elseif($rule->prefix_id == -3) { // ГГГГ
			return date('Y').'/';
		} elseif($rule->prefix_id == -4) { // TODO: Порядковый номер в разрезе года
			$lastPerYear = MedcardHistory::model()->getMaxThroughNumberPerYear(date('Y'), $rule->id);
			if($lastPerYear != null) {
				$number = mb_substr($lastPerYear['to'], mb_strpos($lastPerYear['to'], '|') + 1, mb_strlen($lastPerYear['to']) - mb_strrpos($lastPerYear['to'], '|'));
				return '|'.($number + 1).'|';
			} else {
				return '|1|';
			}
		} elseif($rule->prefix_id > 0) { // Не "не имеется"
			$prefix = MedcardPrefix::model()->findByPk($rule->prefix_id);
			return $prefix->value;
		}
		return '';
	}
	
	private function generatePostfix($rule) {
		if($rule->value == 2 && $this->prevNumber != null && $rule->participle_mode_postfix == 1) {
			$postfix = MedcardPostfix::model()->findByPk($rule->postfix_id);
			return $postfix->value;
		}
		
		if($rule->postfix_id == -2) { // ГГ
			return '/'.date('y');
		} elseif($rule->postfix_id == -3) { // ГГГГ
			return '/'.date('Y');
		} elseif($rule->postfix_id == -4) { // TODO: Порядковый номер в разрезе года
			$lastPerYear = MedcardHistory::model()->getMaxThroughNumberPerYear(date('Y'), $rule->id);
			if($lastPerYear != null) {
				$number = mb_substr($lastPerYear['to'], mb_strpos($lastPerYear['to'], '#') + 1, mb_strlen($lastPerYear['to']) - mb_strrpos($lastPerYear['to'], '#'));
				return '#'.($number + 1).'#';
			} else {
				return '#1#';
			}
		} elseif($rule->postfix_id > 0) { // Не "не имеется"
			$postfix = MedcardPostfix::model()->findByPk($rule->postfix_id);
			return $postfix->value;
		}
		return '';
	}
	
	private function generate($prefix, $postfix, $rule) {
		if($rule->prefix_id == -2) {
			$year = '20'.mb_substr($prefix, 0, mb_strlen($prefix) - 1);
		} elseif($rule->prefix_id == -3) {
			$year = mb_substr($prefix, 2, 2);
		} elseif($rule->postfix_id == -2) {
			$year = '20'.mb_substr($postfix, 1, 2);
		} elseif($rule->postfix_id == -3) {
			$year = mb_substr($postfix, 3);
		} else {
			$year = date('y');
		}
		
		if($rule->value == 0) { // Каждый год с первого номера
			$medcard = MedcardHistory::model()->getMaxThroughNumberPerYear($year, $rule->id);
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
				$number = $this->prevNumber;
				$prevRuleModel = MedcardRule::model()->findByPk($rule->parent_id);
				if($rule->participle_mode_prefix === 0) { // Режим добавления второго префикса
					$number = $prefix.$number;
				}
				if($rule->participle_mode_prefix == 1) { // Режим замены префикса
					$number = $this->splicePrefix($prevRuleModel->prefix_id, $number);
					$number = $prefix.$number;
				}

				if($rule->participle_mode_postfix === 0) { // Режим добавления второго постфикса
					$number = $number.$postfix;
				}
				if($rule->participle_mode_postfix == 1) { // Режим замены постфикса
					$number = $this->splicePostfix($prevRuleModel->postfix_id, $number);
					$number = $number.$postfix;
				}
				return $number;
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
		$data = $this->splicePrefix($prefix_id, $data);
		$data = $this->splicePostfix($postfix_id, $data);
		return $data;
	}
	
	private function splicePrefix($prefix_id, $data, $returnPrefix = false) {
		if($prefix_id !== false) { // Если оно равно false, то это означает, что резать эту частицу не надо
			if($prefix_id == -2 || $prefix_id == -3) {
				if($returnPrefix === false) {
					$data = mb_substr($data, strpos($data, '/') + 1); 
				} else {
					$data =  mb_substr($data, 0, strpos($data, '/')); 
				}
			} elseif($prefix_id != null) {
				$prefixModel = MedcardPrefix::model()->findByPk($prefix_id);
				if($returnPrefix === false) {
					$data = mb_substr($data, mb_strpos($data, $prefixModel->value) + mb_strlen($prefixModel->value));
				} else {
					$data = mb_substr($data, 0, mb_strpos($data, $prefixModel->value));
				}
			}
		}
		
		return $data;
	}
	private function splicePostfix($postfix_id, $data, $returnPostfix = false) {
		if($postfix_id !== false) { // Аналогично
			if($postfix_id == -2 || $postfix_id == -3) {
				if($returnPostfix === false) {
					$data = mb_substr($data, 0, strrpos($data, '/')); 
				} else {
					$data = mb_substr($data, strrpos($data, '/') + 1); 
				}
			} elseif($postfix_id != null) {
				$postfixModel = MedcardPostfix::model()->findByPk($postfix_id);
				if($returnPostfix === false) {
					$data = mb_substr($data, 0, mb_strrpos($data, $postfixModel->value));
				} else {
					$data = mb_substr($data, mb_strrpos($data, $postfixModel->value));
				}
			}	
		}
		return $data;
	}
}

?>