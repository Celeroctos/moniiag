<?php
class CardnumberGenerator extends CComponent {
	private $prevNumber = null;
	private $medcardNumberPrefix;
	private $medcardNumberPostfix;
	private $medcardNumber;
	private $withSave; // generate with saving
	private $clearPrevNumber; // clearing previos cardNumber. This is options, but recommended if you want generate cardNumber every function call
	
	public function __construct($withSave = false, $clearPrevNumber = false) {
		$this->withSave = $withSave;
		$this->clearPrevNumber = $clearPrevNumber;
	}
	
	public function generateNumber($ruleId) {
		$rule = MedcardRule::model()->findByPk($ruleId);
		if($rule == null) {
			return null;
		}
		
		$savedNumber = Yii::app()->user->getState('savedCardNumber', -1);
		if($this->clearPrevNumber) {
			Yii::app()->user->setState('savedCardNumber', -1);
		}		
		if($savedNumber != -1) {
			return $savedNumber;
		} else {
			$number = $this->generate($this->generatePrefix($rule), $this->generatePostfix($rule), $rule);
			if($this->withSave) {
				Yii::app()->user->setState('savedCardNumber', $number);
			}
			return $number;
		}
	}
	
	public function setPrevNumber($number) {
		$this->prevNumber = $number;
	}
	
	public function getPrefix() {
		return $this->medcardNumberPrefix;
	}
	
	public function getPostfix() {
		return $this->medcardNumberPostfix;
	}	
	
	public function getOnlyNumber() {
		return $this->medcardNumber;
	}
	
	private function generatePrefix($rule) {
		// Выясняем сепаратор
		if($rule->prefix_id != null) {
			$separator = MedcardSeparator::model()->findByPk($rule->prefix_separator_id);
			if($separator == null) {
				echo CJSON::encode(
					array(
						'success' => false,
						'errors' => array(
							'separators' => array(
								'Разделитель для префикса не найден!'
							)
						)
					)
				);
				exit();
			}
		}
		
		if($rule->value == 2 && $this->prevNumber != null && $rule->participle_mode_prefix == 1) {
			$prefix = MedcardPostfix::model()->findByPk($rule->prefix_id);
			return $separator->value.$prefix->value;
		}
	
		if($rule->prefix_id == -2) { // ГГ
			return date('y').$separator->value;
		} elseif($rule->prefix_id == -3) { // ГГГГ
			return date('Y').$separator->value;
		} elseif($rule->prefix_id == -4) { // TODO: Порядковый номер в разрезе года
			$lastPerYear = MedcardHistory::model()->getMaxThroughNumberPerYear(date('Y'), $rule->id);
			if($lastPerYear != null) {
				$number = mb_substr($lastPerYear['to'], mb_strpos($lastPerYear['to'], $separator->value) + 1, mb_strlen($lastPerYear['to']) - mb_strrpos($lastPerYear['to'], $separator->value));
				return $separator->value.($number + 1).$separator->value;
			} else {
				return $separator->value.'1'.$separator->value;
			}
		} elseif($rule->prefix_id > 0) { // Не "не имеется"
			$prefix = MedcardPrefix::model()->findByPk($rule->prefix_id);
			return $prefix->value.$separator->value;
		}
		return '';
	}
	
	private function generatePostfix($rule) {
		// Выясняем сепаратор
		if($rule->postfix_id != null) {
			$separator = MedcardSeparator::model()->findByPk($rule->postfix_separator_id);
			if($separator == null ) {
				echo CJSON::encode(
					array(
						'success' => false,
						'errors' => array(
							'separators' => array(
								'Разделитель для постфикса не найден!'
							)
						)
					)
				);
				exit();
			}
		}
		
		if($rule->value == 2 && $this->prevNumber != null && $rule->participle_mode_postfix == 1) {
			$postfix = MedcardPostfix::model()->findByPk($rule->postfix_id);
			return $separator->value.$postfix->value;
		}
		
		if($rule->postfix_id == -2) { // ГГ
			return $separator->value.date('y');
		} elseif($rule->postfix_id == -3) { // ГГГГ
			return $separator->value.date('Y');
		} elseif($rule->postfix_id == -4) { // TODO: Порядковый номер в разрезе года
			$lastPerYear = MedcardHistory::model()->getMaxThroughNumberPerYear(date('Y'), $rule->id);
			if($lastPerYear != null) {
				$number = mb_substr($lastPerYear['to'], mb_strpos($lastPerYear['to'], $separator->value) + 1, mb_strlen($lastPerYear['to']) - mb_strrpos($lastPerYear['to'], $separator->value));
				return $separator->value.($number + 1).$separator->value;
			} else {
				return $separator->value.'1'.$separator->value;
			}
		} elseif($rule->postfix_id > 0) { // Не "не имеется"
			$postfix = MedcardPostfix::model()->findByPk($rule->postfix_id);
			return $separator->value.$postfix->value;
		}
		return '';
	}
	
	// Проверка существования карты по правилу у данного ОМС
	public function isIssetMedcard($omsId, $ruleId) {
		if($ruleId == -1) {
			return null;
		}
		$rule = MedcardRule::model()->findByPk($ruleId);
		if($rule == null) {
			return null;
		}
		$prefix = $this->generatePrefix($rule);
		$postfix = $this->generatePostfix($rule);
		$medcard = MedcardHistory::model()->getByPrefixPostfixAndOms($prefix, $postfix, $omsId, $rule);
		return $medcard != null;
	}
	
	private function generate($prefix, $postfix, $rule) {
		$this->medcardNumberPrefix = $prefix;
		$this->medcardNumberPostfix = $postfix;
		
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
				$number = $this->getNumber($rule, $medcard['to']);
			}
		} elseif($rule->value == 1) { // Сквозная
			$medcard = MedcardHistory::model()->getLastNumberThrough();
			// Чтобы узнать сам номер: если год - это префикс, режем с конца. Если постфикс - с начала
			if($medcard == null) {
				$number = 0; // Это для того, чтобы do-while сгенерировал 1чку
			} else {
				$number = $this->getNumber($rule, $medcard['to']);
			}
		} elseif($rule->value == 2) { // На основе существующего номера
			if($this->prevNumber != null) {			
				$number = $this->prevNumber;
				$prevRuleModel = MedcardRule::model()->findByPk($rule->parent_id);
				if($rule->participle_mode_prefix === 0) { // Режим добавления второго префикса
					$number = $prefix.$number;
				}
				if($rule->participle_mode_prefix == 1) { // Режим замены префикса
					$number = $this->splicePrefix($prevRuleModel, $number);
					$number = $prefix.$number;
				}

				if($rule->participle_mode_postfix === 0) { // Режим добавления второго постфикса
					$number = $number.$postfix;
				}
				if($rule->participle_mode_postfix == 1) { // Режим замены постфикса
					$number = $this->splicePostfix($prevRuleModel, $number);
					$number = $number.$postfix;
				}
				// TODO : обработать случай выдачи номера. Вернуть чистый $number;
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
			$this->medcardNumber = $number;
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
	
	private function getNumber($rule, $data) {
		$data = $this->splicePrefix($rule, $data);
		$data = $this->splicePostfix($rule, $data);
		return $data;
	}
	
	private function splicePrefix($rule, $data, $returnPrefix = false) {
		// Выясняем сепаратор
		if($rule->prefix_id != null) {
			$separator = MedcardSeparator::model()->findByPk($rule->prefix_separator_id);
			if($separator == null) {
				echo CJSON::encode(
					array(
						'success' => false,
						'errors' => array(
							'separators' => array(
								'Разделитель для префикса не найден!'
							)
						)
					)
				);
				exit();
			}
		}
		
		if($rule->prefix_id !== false) { // Если оно равно false, то это означает, что резать эту частицу не надо
			if($rule->prefix_id == -2 || $rule->prefix_id == -3) {
				if($returnPrefix === false) {
					$data = mb_substr($data, strpos($data, $separator->value) + 1); 
				} else {
					$data =  mb_substr($data, 0, strpos($data, $separator->value)); 
				}
			} elseif($rule->prefix_id != null) {
				$prefixModel = MedcardPrefix::model()->findByPk($rule->prefix_id);
				if($returnPrefix === false) {
					$data = mb_substr($data, mb_strpos($data, $prefixModel->value.$separator->value) + mb_strlen($prefixModel->value.$separator->value));
				} else {
					$data = mb_substr($data, 0, mb_strpos($data, $prefixModel->value.$separator->value));
				}
			}
		}
		
		return $data;
	}
	private function splicePostfix($rule, $data, $returnPostfix = false) {
		// Выясняем сепаратор
		if($rule->postfix_id != null) {
			$separator = MedcardSeparator::model()->findByPk($rule->postfix_separator_id);
			if($separator == null) {
				echo CJSON::encode(
					array(
						'success' => false,
						'errors' => array(
							'separators' => array(
								'Разделитель для постфикса не найден!'
							)
						)
					)
				);
				exit();
			}
		}
		
		if($rule->postfix_id !== false) { // Аналогично
			if($rule->postfix_id == -2 || $rule->postfix_id == -3) {
				if($returnPostfix === false) {
					$data = mb_substr($data, 0, strrpos($data, $separator->value)); 
				} else {
					$data = mb_substr($data, strrpos($data, $separator->value) + 1); 
				}
			} elseif($rule->postfix_id != null) {
				$postfixModel = MedcardPostfix::model()->findByPk($rule->postfix_id);
				if($returnPostfix === false) {
					$data = mb_substr($data, 0, mb_strrpos($data, $separator->value.$postfixModel->value));
				} else {
					$data = mb_substr($data, mb_strrpos($data, $separator->value.$postfixModel->value));
				}
			}	
		}
		return $data;
	}
}

?>