<?php
class CategorieViewWidget extends CWidget {
    public $formModel = null;
    public $currentPatient = false;
    public $templateType = null;
    public $greetingId = null; // ID приёма
    public $withoutSave = 0; // Флаг, нужно ли делать кнопку сохранения
    public $prefix = ''; // Если есть два одинаковых шаблона на странице, их айдишники надо как-то отличать. Отличаем по префиксу
    public $canEditMedcard = 1; // Может ли редактировать медкарту
    public $medcard = null;
    public $currentDate = null;
    public $historyElements = array(); // Массив истории элементов: по ним воссоздаётся шаблон
    public $historyTree = array(); // Построенное дерево историии
    public $catsByTemplates = array(); // Категории по шаблону
    public $dividedCats = array(); // Поделённые категории

    public function run() {
        $this->createFormModel();
        if($this->currentDate == null) {
            $this->currentDate = date('Y-m-d h:m');
        }
        // Категории нужны, чтобы сформировать первичный шаблон для пациента в том случае, когда у пациента нет перенесённых записей о данном приёме в хистори. Для начала проверим, есть ли шаблон приёма. Если нет - вынимаем категории и помещаем их в историю
        // Т.е. в приём не вносили изменений, шаблона истории нет
        $categories = $this->getCategories($this->templateType);
        echo $this->render('application.modules.doctors.components.widgets.views.CategorieViewWidget', array(
            'categories' => $categories,
            'model' => $this->formModel,
            'currentPatient' => $this->currentPatient,
            'greetingId' => $this->greetingId,
            'withoutSave' => $this->withoutSave,
            'canEditMedcard' => $this->canEditMedcard
        ));
    }

    public function createFormModel() {
        $this->formModel = new FormTemplateDefault();
    }

    // Получить иерархию категорий на странице
    public function getCategories($pageId) {
        $templateModel = new MedcardTemplate();
        $templates = $templateModel->getTemplatesByPageId($pageId);
        // Получаем типы элементов
        $elementModel = new MedcardElement();
        $typesList = $elementModel->getTypesList();
        $categoriesResult = array();
        foreach($templates as $key => $template) {
            $categorieTemplateFill = array();
            // В случае выпадающих списков с множественным выборов стоит разобрать идентификаторы их
            $categorie_ids = CJSON::decode($template['categorie_ids']);
            foreach($categorie_ids as $index => $id) {
				$categorieResult = $this->getCategorie($id, $template['id'], $template['name']);
                $categorieTemplateFill[] = $categorieResult;
            }
            usort($categorieTemplateFill, function($element1, $element2) {
                if($element1['position'] > $element2['position']) {
                    return 1;
                } elseif($element1['position'] < $element2['position']) {
                    return -1;
                } else {
                    return 0;
                }
            });
            $categoriesResult[] = $categorieTemplateFill;

        }
      // echo "<pre>";
      // var_dump($categoriesResult);
       // exit();
        return $categoriesResult;
    }

	public function getCategorie($id = false, $templateId, $templateName) {
		// Выбираем категорию
        $categorie = MedcardCategorie::model()->findByPk($id);
        if($categorie == null) {
            return;
        }
        $historyCategorie = MedcardElementForPatient::model()->find('history_id = 1 AND categorie_id = :categorie_id AND greeting_id = :greeting_id AND path = :path AND medcard_id = :medcard_id', array(
            ':categorie_id' => $id,
            ':greeting_id' => $this->greetingId,
            ':path' => $categorie->path,
            ':medcard_id' => $this->medcard['card_number'])
        );
        /* В противом случае, находим категорию, как категорию из хистори.
               По ключам: номер приёма, максимальный размер истории (история у категории всегда единичка и не меняется, т.к. категория не изменяется), ключ категории */
        if($historyCategorie == null) {
            if($categorie->path == null) {
                exit('Ошибка: категории c ID '.$categorie['id'].' не имеет пути в шаблоне!');
            }

            $medcardCategorie = new MedcardElementForPatient();
            $medcardCategorie->medcard_id = $this->medcard['card_number'];
            $medcardCategorie->history_id = 1;
            $medcardCategorie->greeting_id = $this->greetingId;
            $medcardCategorie->categorie_name = $categorie->name;
            $medcardCategorie->path = $categorie->path;
            $medcardCategorie->is_wrapped = 0;
            $medcardCategorie->categorie_id = $id;
            $medcardCategorie->element_id = -1;
            $medcardCategorie->change_date = $this->currentDate;
            $medcardCategorie->type = -1; // У категории нет типа контрола
            $medcardCategorie->template_id = $templateId;
            $medcardCategorie->template_name = $templateName;
            $medcardCategorie->is_dynamic = $categorie->is_dynamic;

            if(!$medcardCategorie->save()) {
                exit('Не могу перенести категорию из шаблонов!');
            }

            $categorie = $medcardCategorie;
        } else {
            $categorie = $historyCategorie;
        }
		$categorieResult = array();
		if($categorie != null) {
            // Разные поля при разных выборках
            $categorieResult['id'] = $categorie['categorie_id'];
            $categorieResult['name'] = $categorie['categorie_name'];
            $categorieResult['is_dynamic'] = $categorie['is_dynamic'];

            $parts = explode('.', $categorie['path']);
            // Если количество кусков и точек совпадает, то это неверно: в иерархии у этого элемента нет позиции
            if(mb_substr_count($categorie['path'], '.') == count($parts)) {
                exit('Ошибка: категории c ID '.$categorie['categorie_id'].' не присвоена позиция в шаблоне!');
            }
            $parts = array_reverse($parts); // 1 с конца - номер элемента
            $categorieResult['position'] = $parts[0];
            $categorieResult['elements'] = array();

			$elements = MedcardElement::model()->getElementsByCategorie($categorie['categorie_id']);
			foreach($elements as $key => $element) {
                // Проверим наличие элемента в истории
                $historyCategorieElement = MedcardElementForPatient::model()->find('medcard_id = :medcard_id AND greeting_id = :greeting_id AND path = :path', array(
                    ':medcard_id' => $this->medcard['card_number'],
                    ':greeting_id' => $this->greetingId,
                    ':path' => $element['path']
                ));
                if($historyCategorieElement == null) {
                    // Для элемента посмотрим путь на наличие NULL-позиции
                    $parts = explode('.', $element['path']);
                    $parts = array_filter($parts, function($element) {
                        return trim($element) != '';
                    }) ;
                    // Если количество кусков и точек совпадает, то это неверно: в иерархии у этого элемента нет позиции
                    if(mb_substr_count($element['path'], '.') == count($parts)) {
                        exit('Ошибка: элементу c ID '.$element['id'].' не присвоена позиция в категории!');
                    }

                    $medcardCategorieElement = new MedcardElementForPatient();
                    $medcardCategorieElement->medcard_id = $this->medcard['card_number'];
                    $medcardCategorieElement->history_id = 1;
                    $medcardCategorieElement->greeting_id = $this->greetingId;
                    $medcardCategorieElement->categorie_name = '';
                    $medcardCategorieElement->path = $element['path'];
                    $medcardCategorieElement->is_wrapped = $element['is_wrapped'];
                    $medcardCategorieElement->categorie_id = $categorieResult['id'];
                    $medcardCategorieElement->element_id = $element['id'];
                    $medcardCategorieElement->label_before = $element['label'];
                    $medcardCategorieElement->label_after = $element['label_after'];
                    $medcardCategorieElement->size = $element['size'];
                    $medcardCategorieElement->change_date = $this->currentDate;
                    $medcardCategorieElement->type = $element['type']; // У категории нет типа контрола
                    $medcardCategorieElement->guide_id = $element['guide_id'];

                    if(!$medcardCategorieElement->save()) {
                        exit('Не могу перенести элемент из категории '.$categorieResult['id']);
                    }
                }
				// Для выпадающих списков есть справочник
				if(isset($element['guide_id']) && $element['guide_id'] != null) {
					$medguideValuesModel = new MedcardGuideValue();
					$medguideValues = $medguideValuesModel->getRows(false, $element['guide_id']);
					if(count($medguideValues) > 0) {
						$guideValues = array();
						foreach($medguideValues as $value) {
							$guideValues[$value['id']] = $value['value'];
						}
						$element['guide'] = $guideValues;
					} else {
                        $element['guide'] = array();
                    }
                    $element['label'] = $element['label'];
				}

				// Добавляем в форму
				$this->formModel->setSafeRule('f'.$element['id']);
				$this->formModel->setAttributeLabels('f'.$element['id'], $element['label']);
				$fieldName = 'f'.$element['id'];
				$this->formModel->$fieldName = null;
				$element = $this->getFormValue($element);
				$categorieResult['elements'][] = $element;
			}

            usort($categorieResult['elements'], function($element1, $element2) {
                if($element1['position'] > $element2['position']) {
                    return 1;
                } elseif($element1['position'] < $element2['position']) {
                    return -1;
                } else {
                    return 0;
                }
            });

			// Теперь смотрим, есть ли дочерние элементы
			$categoriesChildren = MedcardCategorie::model()->findAll('parent_id = :parent_id', array(':parent_id' => $id));
			if(count($categoriesChildren) > 0) {
				// Дети есть. Для каждого из них вышеописанный процесс повторяется
				$categorieResult['children'] = array();
				foreach($categoriesChildren as $child) {
					$categorieResult['children'][] = $this->getCategorie($child->id, $templateId, $templateName);
				}
			}

		}

		return $categorieResult;
	}


    // Заполнить форму значениями
    public function getFormValue($element, $historyId = false) {
        $medcardId = $this->currentPatient;
        if($this->formModel->medcardId == null) {
            $this->formModel->medcardId = $medcardId;
        }
        if($historyId == false) {
            $historyIdResult = MedcardElementForPatient::model()->getMaxHistoryPointId($element, $medcardId, $this->greetingId);
            if($historyIdResult['history_id_max'] == null) {
                // Если нет значений для данного элемента, можно уже вернуть сам элемент, потому что его нечем заполнить
                if($element['type'] == 3 || $element['type'] == 2) {
                    $element['selected'] = array();
                }
                return $element;
            } else {
                $historyId = $historyIdResult['history_id_max'];
            }
        }

        // Делаем выборку из базы значения
        $elementFinded = MedcardElementForPatient::model()->find(
            'element_id = :element_id
             AND medcard_id = :medcard_id
             AND history_id = :history_id',
            array(':medcard_id' => $medcardId,
                  ':element_id' => $element['id'],
                  ':history_id' => $historyId
                 )
        );
        if($elementFinded != null) {
            $fieldName = 'f'.$element['id'];
            // Если это комбо с множественным выбором
            if($element['type'] == 3) {
                $element['selected'] = array();
                $element['value'] = CJSON::decode($elementFinded['value']);
                foreach($element['value'] as $id) {
                    $element['selected'][$id] = array('selected' => true);
                }
            }
            // Простой выпадающий список
            if($element['type'] == 2) {
                $element['selected'] = array($element['id'] => array('selected' => $elementFinded['value']));
            }
            $this->formModel->$fieldName = $elementFinded->value;
        } else {
            $element['selected'] = array();
        }

        return $element;
    }

    public function drawCategorie($categorie, $form, $model) {
        $this->render('CategorieElement', array(
            'categorie' => $categorie,
            'form' => $form,
            'model' => $model,
            'prefix' => $this->prefix,
            'canEditMedcard' => $this->canEditMedcard
        ));
    }

    public function getFieldsHistoryByDate($date, $medcardId) {
        $this->formModel = new FormTemplateDefault();
        $this->historyElements = MedcardElementForPatient::model()->getValuesByDate($date, $medcardId);
        // Теперь это говно нужно рекурсивно разобрать и построить по шаблону
        $this->makeTree();
        // Теперь поделим категории
        $this->divideTreebyCats();
        $result = $this->render('application.modules.doctors.components.widgets.views.HistoryTree', array(
            'categories' => $this->historyTree,
            'model' => $this->formModel,
            'templates' => $this->catsByTemplates,
            'dividedCats' => $this->dividedCats
        ), true);
        return $result;
    }

    public function divideTreebyCats() {
       // var_dump($this->catsByTemplates);
       // exit();
        foreach($this->catsByTemplates as $id => $templatesCatsIds) {
            $num = count($templatesCatsIds);
            for($i = 0; $i < $num; $i++) {
                $templateId = $templatesCatsIds[$i]['id'];
                if(isset($this->historyTree[$id])) {
                    if(!isset($this->dividedCats[$templateId])) {
                        $this->dividedCats[$templateId] = array(
                            'name' => $templatesCatsIds[$i]['name'],
                            'cats' => array()
                        );
                    }

                    $this->dividedCats[$templateId]['cats'][] = $this->historyTree[$id];
                }
            }
        }
        //var_dump($this->dividedCats);
        //exit();
    }

    // Получить дерево актуальных категорий (не используется)
    public function getCatsByTemplates() {
        $templatesInDb = MedcardTemplate::model()->findAll();
        foreach($templatesInDb as $key => $template) {
            $decodedIds = CJSON::decode($template['categorie_ids']);
            foreach($decodedIds as $id) {
                if(!isset($this->catsByTemplates[$template['id']])) {
                    $this->catsByTemplates[$template['id']] = array();
                }
                // Записываем категории
                $this->catsByTemplates[$template['id']][] = array(
                    'id' => $id,
                    'name' => $template['name']
                );
            }
        }
    }

    public function makeTree() {
        foreach($this->historyElements as $element) {
            $pathArr = explode('.', $element['path']);
            $numParts = count($pathArr);
            $currentNode = &$this->historyTree;
            // Если element_id == -1, то это категория
            for($i = 0; $i < $numParts; $i++) {
                if($i < $numParts - 1) {
                    if(!isset($currentNode[$pathArr[$i]])) {
                        $node = array(
                            'elements' => array(),
                            'children' => array()
                        );
                        $currentNode[$pathArr[$i]] = $node;
                        // Корневую записываем в шаблоны
                        if($i == 0 && $element['template_id'] != null && $element['template_name'] != null) {
                            if(!isset($this->catsByTemplates[$pathArr[$i]])) {
                                $this->catsByTemplates[$pathArr[$i]] = array();
                            }
                            // Записываем категории
                            $this->catsByTemplates[$pathArr[$i]][] = array(
                                'id' => $element['template_id'],
                                'name' => $element['template_name']
                            );
                        }
                    }
                    if($i == $numParts - 2) { // Предпоследняя итерация определяет категорию, предпоследняя итерация определяет элемент
                        if($element['element_id'] == -1) {
                            $currentNode = &$currentNode[$pathArr[$i]]['children'];
                        } else {
                            $currentNode = &$currentNode[$pathArr[$i]];
                        }
                    } else {
                        $currentNode = &$currentNode[$pathArr[$i]]['children'];
                    }
                } else {
                    if($element['element_id'] == -1) { // Только конечный элемент можно рассмотреть как определённый
                        if(!isset($currentNode[$pathArr[$i]])) {
                            $node = array(
                                'elements' => array(),
                                'name' => $element['categorie_name'],
                                'children' => array()
                            );
                            $currentNode[$pathArr[$i]] = $node;
                        } else {
                            $currentNode[$pathArr[$i]]['name'] = $element['categorie_name'];
                        }

                        // Корневую-концевую записываем в шаблоны
                        if($i == 0 && $element['template_id'] != null && $element['template_name'] != null) {
                            if(!isset($this->catsByTemplates[$pathArr[$i]])) {
                                $this->catsByTemplates[$pathArr[$i]] = array();
                            }
                            // Записываем категории
                            $this->catsByTemplates[$pathArr[$i]][] = array(
                                'id' => $element['template_id'],
                                'name' => $element['template_name']
                            );
                        }

                    } else {
                        $data = array(
                            'label' => $element['label_before'],
                            'label_after' => $element['label_after'],
                            'size' => $element['size'],
                            'is_wrapped' => $element['is_wrapped'],
                            'value' => $element['value'],
                            'id' => $element['element_id'],
                            'type' => $element['type'],
                            'guide_id' => $element['guide_id']
                        );
                        if($element['guide_id'] != null) {
                            $medguideValuesModel = new MedcardGuideValue();
                            $medguideValues = $medguideValuesModel->getRows(false, $element['guide_id']);
                            if(count($medguideValues) > 0) {
                                $guideValues = array();
                                foreach($medguideValues as $value) {
                                    $guideValues[$value['id']] = $value['value'];
                                }
                                $data['guide'] = $guideValues;
                            } else {
                                $data['guide'] = array();
                            }
							if($element['type'] == 2 || $element['type'] == 3) {
								$data['selected'] = CJSON::decode($element['value']);
							}
                        }

                        $currentNode['elements'][] = $data;

                        $this->formModel->setSafeRule('f'.$element['element_id']);
                        $this->formModel->setAttributeLabels('f'.$element['element_id'], $element['label_before']);
                        $fieldName = 'f'.$element['element_id'];
                        $this->formModel->$fieldName = null;
                    }
                }
            }
        }
        ksort($this->historyTree);
    }

    public function drawHistoryCategorie($categorie, $cId, $form, $model, $prefix = false, $templateKey) {
        if($prefix === false) {
            $prefix = $this->prefix;
        }
        $this->render('HistoryTreeElement', array(
            'categorie' => $categorie,
            'prefix' => $prefix,
            'form' => $form,
            'model' => $model,
            'cId' => $cId,
            'templateKey' => $templateKey
        ));
    }
}
?>