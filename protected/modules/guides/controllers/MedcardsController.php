<?php
class MedcardsController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'viewprefixes';
	protected $typesList = array(
		'Каждый год с первого номера',
		'Сквозная',
		'На основе существующего номера'
	);

    public function actionViewPrefixes() {
		$this->render('viewprefixes', array(
			'model' => new FormMedcardPrefixAdd()
		));
    }
	
	public function actionViewPostfixes() {
		$this->render('viewpostfixes', array(
			'model' => new FormMedcardPostfixAdd()
		));
    }
	
	public function actionViewSeparators() {
		$this->render('viewseparators', array(
			'model' => new FormMedcardSeparatorAdd()
		));
    }
	
	public function actionViewRules() {
		$prefixesList = array(
			'-1' => 'Не имеется',
			'-2' => 'Предустановленный: ГГ',
			'-3' => 'Предустановленный: ГГГГ',
			'-4' => 'Предустановленный: порядковый номер в разрезе года'
		);
		$postfixesList = array(
			'-1' => 'Не имеется',
			'-2' => 'Предустановленный: ГГ',
			'-3' => 'Предустановленный: ГГГГ',
			'-4' => 'Предустановленный: порядковый номер в разрезе года'
		);
			
		$prefixesDb = MedcardPrefix::model()->findAll();
		$postfixesDb = MedcardPostfix::model()->findAll();
		foreach($prefixesDb as $prefix) {
			$prefixesList[(string)$prefix['id']] = $prefix['value'];
		}
		foreach($postfixesDb as $postfix) {
			$postfixesList[(string)$postfix['id']] = $postfix['value'];
		}
		$rulesList = $this->getMedcardsRules();
		$separatorsList = $this->getSeparatorsList();
	
		$this->render('viewrules', array(
			'prefixesList' => $prefixesList,
			'postfixesList' => $postfixesList,
			'rulesList' => $rulesList,
			'separatorsList' => $separatorsList,
			'typesList' => $this->typesList,
			'model' => new FormMedcardRuleAdd()
		));
    }
	
	public function getMedcardsRules() {
		$rulesList = array('-1' => 'Не наследуется');
		$rulesDb = MedcardRule::model()->findAll();
		foreach($rulesDb as $rule) {
			$rulesList[(string)$rule['id']] = $rule['name'];
		}
		return $rulesList;
	}
	
	public function actionUpdateRulesList() {
		echo CJSON::encode(
			array(
				'success' => true,
				'data' => $this->getMedcardsRules()
			)
		);
	}
		
	public function actionGetRules() {
        try {
            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];
			
			if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
                $filters = CJSON::decode($_GET['filters']);
            } else {
                $filters = false;
            }

            $model = new MedcardRule();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $rules = $model->getRows($filters, $sidx, $sord, $start, $rows);
			foreach($rules as &$rule) {
				if($rule['parent_id'] == null) {
					$rule['parent_id'] = -1;
					$rule['parent'] = 'Нет';
				}
				if($rule['prefix_id'] == null) {
					$rule['prefix_id'] = -1;
					$rule['prefix'] = 'Нет';
				} elseif($rule['prefix_id'] == -2) {
					$rule['prefix'] = 'Предустановленный: ГГ';
				} elseif($rule['prefix_id'] == -3) {
					$rule['prefix'] = 'Предустановленный: ГГГГ';
				} elseif($rule['prefix_id'] == -4) {
					$rule['prefix'] = 'Предустановленный: порядковый номер в разрезе года';
				}
				
				if($rule['postfix_id'] == null) {
					$rule['postfix_id'] = -1;
					$rule['postfix'] = 'Нет';
				} elseif($rule['postfix_id'] == -2) {
					$rule['postfix'] = 'Предустановленный: ГГ';
				} elseif($rule['postfix_id'] == -3) {
					$rule['postfix'] = 'Предустановленный: ГГГГ';
				} elseif($rule['postfix_id'] == -4) {
					$rule['postfix'] = 'Предустановленный: порядковый номер в разрезе года';
				}
				
				if($rule['postfix_separator_id'] == null) {
					$rule['postfix_separator_id'] = -1;
					$rule['postfix_separator'] = '-';
				}
				
				if($rule['prefix_separator_id'] == null) {
					$rule['prefix_separator_id'] = -1;
					$rule['prefix_separator'] = '-';
				}
				
				if($rule['participle_mode_prefix'] === null) {
					$rule['participle_mode_prefix_desc'] = '-';
					$rule['participle_mode_prefix'] = -1;
				} elseif($rule['participle_mode_prefix'] == 0) {
					$rule['participle_mode_prefix_desc'] = 'Добавление второго';
				} elseif($rule['participle_mode_prefix'] == 1) {
					$rule['participle_mode_prefix_desc'] = 'Замена';
				} 
				
				if($rule['participle_mode_postfix'] === null) {
					$rule['participle_mode_postfix_desc'] = '-';
					$rule['participle_mode_postfix'] = -1;
				} elseif($rule['participle_mode_postfix'] == 0) {
					$rule['participle_mode_postfix_desc'] = 'Добавление второго';
				} elseif($rule['participle_mode_postfix'] == 1) {
					$rule['participle_mode_postfix_desc'] = 'Замена';
				} 
				
				if($rule['type'] == 1) {
					$rule['type_desc'] = 'Стационарная';
				} else {
					$rule['type_desc'] = 'Амбулаторная';
				}
				
				$rule['rule'] = $this->typesList[$rule['value']];
			}
			
            echo CJSON::encode(
                array(
                    'success' => true,
                    'rows' => $rules,
                    'total' => $totalPages,
                    'records' => count($num))
            );
			
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
	
		
	public function actionGetPostfixes() {
        try {
            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];
			
			if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
                $filters = CJSON::decode($_GET['filters']);
            } else {
                $filters = false;
            }

            $model = new MedcardPostfix();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $postfixes = $model->getRows($filters, $sidx, $sord, $start, $rows);
            echo CJSON::encode(
                array(
                    'success' => true,
                    'rows' => $postfixes,
                    'total' => $totalPages,
                    'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

	public function actionGetPrefixes() {
        try {
            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];
			
			if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
                $filters = CJSON::decode($_GET['filters']);
            } else {
                $filters = false;
            }

            $model = new MedcardPrefix();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $prefixes = $model->getRows($filters, $sidx, $sord, $start, $rows);
            echo CJSON::encode(
                array(
                    'success' => true,
                    'rows' => $prefixes,
                    'total' => $totalPages,
                    'records' => count($num))
            );
			
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
	
	public function actionGetSeparators() {
        try {
            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];
			
			if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
                $filters = CJSON::decode($_GET['filters']);
            } else {
                $filters = false;
            }

            $model = new MedcardSeparator();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $separators = $model->getRows($filters, $sidx, $sord, $start, $rows);
            echo CJSON::encode(
                array(
                    'success' => true,
                    'rows' => $separators,
                    'total' => $totalPages,
                    'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
	
	
	public function actionGetOnePrefix($id) {
        $prefix = MedcardPrefix::model()->findByPk($id);
        echo CJSON::encode(
			array(
				'success' => true,
                'data' => $prefix
			)
        );
    }

	public function actionDeletePrefix($id) {
        try {
            MedcardPrefix::model()->deleteByPk($id);
            echo CJSON::encode(array('success' => 'true',
									 'text' => 'Префикс успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }

    private function addEditModelPrefix($prefix, $model, $msg) {
        $prefix->value = $model->value;
        if($prefix->save()) {
            echo CJSON::encode(array(
					'success' => true,
                    'text' =>  $msg
                )
            );
        }
    }

    public function actionEditPrefix() {
        $model = new FormMedcardPrefixAdd();
        if(isset($_POST['FormMedcardPrefixAdd'])) {
            $model->attributes = $_POST['FormMedcardPrefixAdd'];
            if($model->validate()) {
                $prefix = MedcardPrefix::model()->findByPk($_POST['FormMedcardPrefixAdd']['id']);
                $this->addEditModelPrefix($prefix, $model, 'Префикс успешно отредактирован.');
            } else {
                echo CJSON::encode(array(
					'success' => 'false',
                    'errors' => $model->errors
					)
				);
            }
        }
    }

    public function actionAddPrefix() {
        $model = new FormMedcardPrefixAdd();
        if(isset($_POST['FormMedcardPrefixAdd'])) {
            $model->attributes = $_POST['FormMedcardPrefixAdd'];
            if($model->validate()) {
                $prefix = new MedcardPrefix();
                $this->addEditModelPrefix($prefix, $model, 'Префикс успешно добавлен.');
            } else {
                echo CJSON::encode(array(
					'success' => 'false',
                    'errors' => $model->errors
					)
				);
            }
        }
    }
	
	public function actionGetOnePostfix($id) {
        $postfix = MedcardPostfix::model()->findByPk($id);
        echo CJSON::encode(
			array(
				'success' => true,
                'data' => $postfix
			)
        );
    }

	public function actionDeletePostfix($id) {
        try {
            MedcardPostfix::model()->deleteByPk($id);
            echo CJSON::encode(array('success' => 'true',
									 'text' => 'Постфикс успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }

    private function addEditModelPostfix($postfix, $model, $msg) {
        $postfix->value = $model->value;
        if($postfix->save()) {
            echo CJSON::encode(array(
					'success' => true,
                    'text' =>  $msg
                )
            );
        }
    }

    public function actionEditPostfix() {
        $model = new FormMedcardPostfixAdd();
        if(isset($_POST['FormMedcardPostfixAdd'])) {
            $model->attributes = $_POST['FormMedcardPostfixAdd'];
            if($model->validate()) {
                $postfix = MedcardPostfix::model()->findByPk($_POST['FormMedcardPostfixAdd']['id']);
                $this->addEditModelPostfix($postfix, $model, 'Постфикс успешно отредактирован.');
            } else {
                echo CJSON::encode(array(
					'success' => 'false',
                    'errors' => $model->errors
					)
				);
            }
        }
    }

    public function actionAddPostfix() {
        $model = new FormMedcardPostfixAdd();
        if(isset($_POST['FormMedcardPostfixAdd'])) {
            $model->attributes = $_POST['FormMedcardPostfixAdd'];
            if($model->validate()) {
                $postfix = new MedcardPostfix();
                $this->addEditModelPostfix($postfix, $model, 'Постфикс успешно добавлен.');
            } else {
                echo CJSON::encode(array(
					'success' => 'false',
                    'errors' => $model->errors
					)
				);
            }
        }
    }
	
	public function actionGetOneRule($id) {
        $rule = MedcardRule::model()->findByPk($id);
		if($rule['parent_id'] === null) {
			$rule['parent_id'] = -1;
		}
		if($rule['prefix_id'] === null) {
			$rule['prefix_id'] = -1;
		}
		if($rule['postfix_id'] === null) {
			$rule['postfix_id'] = -1;
		}
		if($rule['prefix_separator_id'] === null) {
			$rule['prefix_separator_id'] = -1;
		}
		if($rule['postfix_separator_id'] === null) {
			$rule['postfix_separator_id'] = -1;
		}
		if($rule['participle_mode_prefix'] === null) {
			$rule['participle_mode_prefix'] = -1;
		}
		if($rule['participle_mode_postfix'] === null) {
			$rule['participle_mode_postfix'] = -1;
		}

        echo CJSON::encode(
			array(
				'success' => true,
                'data' => $rule
			)
        );
    }

	public function actionDeleteRule($id) {
        try {
			if(MedcardHistory::model()->find('rule_id = :rule_id', array(':rule_id' => $id)) != null) {
				echo CJSON::encode(
					array(
						'success' => false,
						'errors' => array(
							'rules' => array(
								'Для данного правила существуют медкарты. Удаление правила недопустимо!'
							)
						)
					)
				);
				exit();
			}
            MedcardRule::model()->deleteByPk($id);
            echo CJSON::encode(array('success' => 'true',
									 'text' => 'Правило успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }

    private function addEditModelRule($rule, $model, $msg) {
		if($model->postfixId != -1 && $model->prefixId != -1 && $model->postfixSeparatorId == $model->prefixSeparatorId) {
			echo CJSON::encode(
				array(
					'success' => false,
					'errors' => array(
						'separators' => array(
							'Для префикса и постфикса используется один и тот же разделитель, что недопустимо!'
						)
					)
				)
			);
			exit();
		}
        $rule->value = $model->typeId;
		$rule->name = $model->name;
		if($model->prefixId != -1) {
			$rule->prefix_id = $model->prefixId;
			$rule->prefix_separator_id = $model->prefixSeparatorId;
		} else {
			$rule->prefix_id = null;
			$rule->prefix_separator_id = null;
		}
		
		if($model->postfixId != -1) {
			$rule->postfix_id = $model->postfixId;
			$rule->postfix_separator_id = $model->postfixSeparatorId;
		} else {
			$rule->postfix_id = null;
			$rule->postfix_separator_id = null;
		}
		
		if($model->parentId != -1) {
			$rule->parent_id = $model->parentId;
			if($rule->participle_mode_prefix != -1) {
				$rule->participle_mode_prefix = $model->participleModePrefix;
			} else {
				$rule->participle_mode_prefix = null;
			}
			
			if($rule->participle_mode_postfix != -1) {
				$rule->participle_mode_postfix = $model->participleModePostfix;
			} else {
				$rule->participle_mode_postfix = null;
			}
			
		} else {
			$rule->parent_id = null;
		}
		
		$rule->type = $model->cardType;
		
        if($rule->save()) {
            echo CJSON::encode(array(
					'success' => true,
                    'text' =>  $msg
                )
            );
        }
    }

    public function actionEditRule() {
        $model = new FormMedcardRuleAdd();
        if(isset($_POST['FormMedcardRuleAdd'])) {
            $model->attributes = $_POST['FormMedcardRuleAdd'];
            if($model->validate()) {
                $rule = MedcardRule::model()->findByPk($_POST['FormMedcardRuleAdd']['id']);
                $this->addEditModelRule($rule, $model, 'Правило успешно отредактирован.');
            } else {
                echo CJSON::encode(array(
					'success' => 'false',
                    'errors' => $model->errors
					)
				);
            }
        }
    }

    public function actionAddRule() {
        $model = new FormMedcardRuleAdd();
        if(isset($_POST['FormMedcardRuleAdd'])) {
            $model->attributes = $_POST['FormMedcardRuleAdd'];
            if($model->validate()) {
                $rule = new MedcardRule();
                $this->addEditModelRule($rule, $model, 'Правило успешно добавлен.');
            } else {
                echo CJSON::encode(array(
					'success' => 'false',
                    'errors' => $model->errors
					)
				);
            }
        }
    }
	
	public function actionGetOneSeparator($id) {
        $separator = MedcardSeparator::model()->findByPk($id);
        echo CJSON::encode(
			array(
				'success' => true,
                'data' => $separator
			)
        );
    }

	public function actionDeleteSeparator($id) {
        try {
            MedcardSeparator::model()->deleteByPk($id);
            echo CJSON::encode(array('success' => 'true',
									 'text' => 'Разделитель успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array(
				'success' => 'false',
                'error' => 'На данную запись есть ссылки!'
			));
        }
    }

    private function addEditModelSeparator($separator, $model, $msg) {
        $issetSeparator = MedcardSeparator::model()->find('value = :value', array(':value' => $model->value));
		if($issetSeparator != null) {
			echo CJSON::encode(array(
					'success' => false,
                    'errors' =>  array(
						'value' => array(
							'Такой разделитель уже существует в списке разделителей!'
						)
					)
                )
            );
			exit();
		}
		$separator->value = $model->value;
        if($separator->save()) {
            echo CJSON::encode(array(
					'success' => true,
                    'text' =>  $msg
                )
            );
        }
    }

    public function actionEditSeparator() {
        $model = new FormMedcardSeparatorAdd();
        if(isset($_POST['FormMedcardSeparatorAdd'])) {
            $model->attributes = $_POST['FormMedcardSeparatorAdd'];
            if($model->validate()) {
                $separator = MedcardSeparator::model()->findByPk($_POST['FormMedcardSeparatorAdd']['id']);
                $this->addEditModelSeparator($separator, $model, 'Разделитель успешно отредактирован.');
            } else {
                echo CJSON::encode(array(
					'success' => 'false',
                    'errors' => $model->errors
					)
				);
            }
        }
    }

    public function actionAddSeparator() {
        $model = new FormMedcardSeparatorAdd();
        if(isset($_POST['FormMedcardSeparatorAdd'])) {
            $model->attributes = $_POST['FormMedcardSeparatorAdd'];
            if($model->validate()) {
                $postfix = new MedcardSeparator();
                $this->addEditModelSeparator($postfix, $model, 'Разделитель успешно добавлен.');
            } else {
                echo CJSON::encode(array(
					'success' => 'false',
					'errors' => $model->errors
					)
				);
            }
        }
    }
	
	private function getSeparatorsList($onlyNotUsed = false) {
		$separatorsDb = $onlyNotUsed ? MedcardSeparator::model()->findAllNotUsed() :  MedcardSeparator::model()->findAll();
		$separatorsList = array();
		foreach($separatorsDb as $separator) {
			$separatorsList[(string)$separator['id']] = $separator['value'];
		}
		return $separatorsList;
	}
	public function updateSeparatorsList() {
		 echo CJSON::encode(array(
			'success' => true,
			'data' => $this->getSeparatorsList()
			)
		);
	}

}

?>