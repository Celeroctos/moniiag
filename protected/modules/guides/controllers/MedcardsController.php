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
	
	public function actionViewRules() {
		$prefixesList = array(
			'-1' => 'Не имеется',
			'-2' => 'Предустановленный: ГГ',
			'-3' => 'Предустановленный: ГГГГ'
		);
		$postfixesList = array(
			'-1' => 'Не имеется',
			'-2' => 'Предустановленный: ГГ',
			'-3' => 'Предустановленный: ГГГГ'
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
	
		$this->render('viewrules', array(
			'prefixesList' => $prefixesList,
			'postfixesList' => $postfixesList,
			'rulesList' => $rulesList,
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
				}
				
				if($rule['postfix_id'] == null) {
					$rule['postfix_id'] = -1;
					$rule['postfix'] = 'Нет';
				} elseif($rule['postfix_id'] == -2) {
					$rule['postfix'] = 'Предустановленный: ГГ';
				} elseif($rule['postfix_id'] == -3) {
					$rule['postfix'] = 'Предустановленный: ГГГГ';
				}
				
				if($rule['participle_mode'] === null) {
					$rule['participle_mode_desc'] = '-';
					$rule['participle_mode'] = -1;
				} elseif($rule['participle_mode'] == 0) {
					$rule['participle_mode_desc'] = 'Добавление вторых';
				} elseif($rule['participle_mode'] == 1) {
					$rule['participle_mode_desc'] = 'Замена';
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
		if($rule['parent_id'] == null) {
			$rule['parent_id'] = -1;
		}
		if($rule['prefix_id'] == null) {
			$rule['prefix_id'] = -1;
		}
		if($rule['postfix_id'] == null) {
			$rule['postfix_id'] = -1;
		}
		if($rule['participle_mode'] === null) {
			$rule['participle_mode'] = -1;
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
        $rule->value = $model->typeId;
		$rule->name = $model->name;
		if($model->prefixId != -1) {
			$rule->prefix_id = $model->prefixId;
		} else {
			$rule->prefix_id = null;
		}
		if($model->postfixId != -1) {
			$rule->postfix_id = $model->postfixId;
		} else {
			$rule->postfix_id = null;
		}
		if($model->parentId != -1) {
			$rule->parent_id = $model->parentId;
			if($rule->participle_mode != -1) {
				$rule->participle_mode = $model->participleMode;
			} else {
				$rule->participle_mode = null;
			}
		} else {
			$rule->parent_id = null;
		}
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

}

?>