<?php
class CardnumberGenerator extends CComponent {
	public function generateNumber($ruleId) {
		$rule = MedcardRule::model()->findByPk($ruleId);
		if($rule == null) {
			return null;
		}

		$number = $this->generate($this->generatePrefix($rule), $this->generatePostfix($rule), $rule);
	}
	
	private function generatePrefix($rule) {
		if($rule->prefix_id == -2) { // ГГ
			return date('y').'/';
		} elseif($rule->prefix_id == -3) { // ГГГГ
			return date('Y').'/';
		}
		return '';
	}
	
	private function generatePostfix($rule) {
		if($rule->postfix_id == -2) { // ГГ
			return '/'.date('y');
		} elseif($rule->postfix_id == -3) { // ГГГГ
			return '/'.date('Y');
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
		}

		if($rule->value == 0) { // Каждый год с первого номера
			$medcard = MedcardHistory::model()->getLastNumberByYear($year, $rule->id);
			// Чтобы узнать сам номер: если год - это префикс, режем с конца. Если постфикс - с начала
			if($rule->prefix_id == -2 || $rule->prefix_id == -3) {
				$number = mb_substr($medcard['to'], strpos($medcard['to'], '/')); 
			} 
			if($rule->postfix_id == -2 || $rule->postfix_id == -3) {
				$number = mb_substr($medcard['to'], 0, strrpos($medcard['to'], '/')); 
			} 
		} elseif($rule->value == 1) { // Сквозная
			$medcard = MedcardHistory::model()->getLastNumberThrough();
			// Чтобы узнать сам номер: если год - это префикс, режем с конца. Если постфикс - с начала
			if($rule->prefix_id == -2 || $rule->prefix_id == -3) {
				$number = mb_substr($medcard['to'], strpos($medcard['to'], '/')); 
			} 
			if($rule->postfix_id == -2 || $rule->postfix_id == -3) {
				$number = mb_substr($medcard['to'], 0, strrpos($medcard['to'], '/')); 
			} 
		} elseif($rule->value == 2) { // На основе существующего номера
			
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

			$issetMedcard = MedcardHistory::model()->find('t.to = :to', array(':to' => $str));
			if(!$issetMedcard) {
				$flag = false;
			}
		} while($flag);
		var_dump($str);
	}
}

?>