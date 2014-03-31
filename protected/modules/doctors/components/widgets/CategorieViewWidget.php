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
    public $templatePrefix = null;
    public $templateId = null; // Это айдишник шаблона

    public function run() {
       /* $deps = MedcardElementPatientDependence::model()->getDistinctDependences();
        foreach($deps as $dep) {
            $dep2 = MedcardElementDependence::model()->find(
                'dep_element_id = :dep_element_id
                AND value_id = :value_id
                AND element_id = :element_id
                AND action = :action',
                array(
                    ':dep_element_id' => $dep['dep_element_id'],
                    ':value_id' => $dep['value'],
                    ':element_id' => $dep['element_id'],
                    ':action' => $dep['action']
                )
            );
            if($dep2 == null) {
                var_dump("?");
                $dep2 = new MedcardElementDependence();
                $dep2->element_id = $dep['element_id'];
                $dep2->value_id = $dep['value'];
                $dep2->dep_element_id = $dep['dep_element_id'];
                $dep2->action = $dep['action';
                if(!$dep2->save()) {
                    exit("!");
                }
            }
        }
        var_dump($deps);
        exit(); */

        ini_set('max_execution_time', 60);
        $this->createFormModel();
        if($this->currentDate == null) {
            $this->currentDate = date('Y-m-d h:i');
        }
        // Категории нужны, чтобы сформировать первичный шаблон для пациента в том случае, когда у пациента нет перенесённых записей о данном приёме в хистори. Для начала проверим, есть ли шаблон приёма. Если нет - вынимаем категории и помещаем их в историю
        // Т.е. в приём не вносили изменений, шаблона истории нет
        if($this->greetingId == null || $this->medcard == null) {
            $categories = array();
        } else {
            $categories = $this->getCategories($this->templateType, $this->templateId);
        }

        echo $this->render('application.modules.doctors.components.widgets.views.CategorieViewWidget', array(
            'categories' => $categories,
            'model' => $this->formModel,
            'currentPatient' => $this->currentPatient,
            'greetingId' => $this->greetingId,
            'greeting' => ($this->greetingId != null) ? SheduleByDay::model()->findByPk($this->greetingId) : null,
            'withoutSave' => $this->withoutSave,
            'lettersInPixel' => Setting::model()->find('module_id = -1 AND name = :name', array(':name' => 'lettersInPixel'))->value,
            'canEditMedcard' => $this->canEditMedcard,
            'templatePrefix' => $this->templatePrefix
        ));
    }

    public function createFormModel() {
        $this->formModel = new FormTemplateDefault();
    }

    private function getHistoryElements($mode = 'one', $data = array()) {
        $conditions = '';
        if(isset($data[':history_id'])) {
            if($conditions == '') {
                $conditions = 'history_id = :history_id';
            } else {
                $conditions .= ' AND history_id = :history_id';
            }
        }

        if(isset($data[':greeting_id'])) {
            if($conditions == '') {
                $conditions = 'greeting_id = :greeting_id';
            } else {
                $conditions .= ' AND greeting_id = :greeting_id';
            }
        }

        if(isset($data[':medcard_id'])) {
            if($conditions == '') {
                $conditions = 'medcard_id = :medcard_id';
            } else {
                $conditions .= ' AND medcard_id = :medcard_id';
            }
        }

        if(isset($data[':path'])) {
            if($conditions == '') {
                $conditions = 'path = :path';
            } else {
                $conditions .= ' AND path = :path';
            }
        }

        if(isset($data[':categorie_id'])) {
            if($conditions == '') {
                $conditions = 'categorie_id = :categorie_id';
            } else {
                $conditions .= ' AND categorie_id = :categorie_id';
            }
        }

        if(isset($data[':element_id'])) {
            if($conditions == '') {
                $conditions = 'element_id != :element_id';
            } else {
                $conditions .= ' AND element_id != :element_id';
            }
        }

        if($mode == 'one') {
            return MedcardElementForPatient::model()->find(
                $conditions,
                $data
            );
        } elseif($mode == 'multiple') {
            return MedcardElementForPatient::model()->findAll(
                $conditions,
                $data
            );
        }
    }

    // Получить иерархию категорий на странице
    public function getCategories($pageId, $templateId = null) {
        $templateModel = new MedcardTemplate();
        if($templateId == null) {
            $templates = $templateModel->getTemplatesByPageId($pageId);
        } else {
            $template = $templateModel->findByPk($templateId);
            if($template == null) {
                exit('Невозможно найти шаблон с ID '.$templateId.'!');
            }
            $templates = array(array(
               'id' => $template->id,
               'name' => $template->name,
               'page_id' => $template->page_id,
               'categorie_ids' => $template->categorie_ids
            ));
        }
        // Получаем типы элементов
        $elementModel = new MedcardElement();
        $typesList = $elementModel->getTypesList();
        $categoriesResult = array();
        foreach($templates as $key => $template) {
            $categorieTemplateFill = array();
            // В случае выпадающих списков с множественным выборов стоит разобрать идентификаторы их
            $categorie_ids = CJSON::decode($template['categorie_ids']);
            foreach($categorie_ids as $index => $id) {
                // Попробуем выбрать категорию из истории. Если не получится, то нужно создавать новую. В противном случае, брать из истории
                $categorie = MedcardCategorie::model()->findByPk($id);
                if($categorie == null) {
                    continue;
                }

                $historyCategorie = $this->getHistoryElements('one', array(
                    ':greeting_id' => $this->greetingId,
                    ':medcard_id' => $this->medcard['card_number'],
                    ':history_id' => 1,
                    ':path' => $categorie->path
                ));

                if($historyCategorie == null) {
                    $categorieResult = $this->getCategorie($id, $template['id'], $template['name']);
                } else {
                    $categorieResult = $this->getCategorie(false, $template['id'], $template['name'], $historyCategorie->path);
                }
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
            $categoriesResult[] = array(
                'templateName' => $template['name'],
                'cats' => $categorieTemplateFill
            );

        }
     //   echo "<pre>";
//var_dump($categoriesResult);
     //   exit();
        return $categoriesResult;
    }

	public function getCategorie($id = false, $templateId, $templateName, $path = false) {
		// Выбираем категорию
        if($id && !$path) { // Категории, для которых делается выборка по-первому
            $categorie = MedcardCategorie::model()->findByPk($id);
            if($categorie == null) {
                return array();
            }

            $historyCategories = $this->getHistoryElements('multiple',  array(
                ':greeting_id' => $this->greetingId,
                ':medcard_id' => $this->medcard['card_number'],
                ':history_id' => 1,
                ':categorie_id' => $categorie->parent_id,
                ':path' => $categorie->path
            ));
        } else {
            $historyCategories = $this->getHistoryElements('multiple',  array(
                ':greeting_id' => $this->greetingId,
                ':medcard_id' => $this->medcard['card_number'],
                ':history_id' => 1,
                ':path' => $path
            ));
        }
        /* В противом случае, находим категорию, как категорию из хистори.
               По ключам: номер приёма, максимальный размер истории (история у категории всегда единичка и не меняется, т.к. категория не изменяется), ключ категории */
        if(count($historyCategories) == 0) {
			if(!isset($categorie)) {
				$categorie = MedcardCategorie::model()->findByPk($id);
			}
            if(isset($categorie) && $categorie->path == null) {
                exit('Ошибка: категории c ID '.$categorie['id'].' не имеет пути в шаблоне!');
            }

            $medcardCategorie = new MedcardElementForPatient();
            $medcardCategorie->medcard_id = $this->medcard['card_number'];
            $medcardCategorie->history_id = 1;
            $medcardCategorie->greeting_id = $this->greetingId;
            $medcardCategorie->categorie_name = $categorie->name;
	   // $medcardCategorie->is_required = $categorie->is_required;
            $medcardCategorie->path = $categorie->path;
            $medcardCategorie->is_wrapped = 0;
            $medcardCategorie->categorie_id = $categorie->parent_id;
            $medcardCategorie->element_id = -1;
            $medcardCategorie->change_date = $this->currentDate;
            $medcardCategorie->type = -1; // У категории нет типа контрола
            $medcardCategorie->template_id = $templateId;
            $medcardCategorie->template_name = $templateName;
            $medcardCategorie->is_dynamic = $categorie->is_dynamic;
            $medcardCategorie->real_categorie_id = $categorie->id;

            if(!$medcardCategorie->save()) {
                exit('Не могу перенести категорию из шаблонов!');
            }

            $historyCategories[] = $medcardCategorie;
        } else {
            // Выбираем ещё и дополнительные категории (клонированные). Они не попадают в первый раз по условию пути
            if(isset($categorie) && $categorie != null) {
                $historyCategoriesPlus = $this->getHistoryElements('multiple', array(
                    ':greeting_id' => $this->greetingId,
                    ':medcard_id' => $this->medcard['card_number'],
                    ':history_id' => 1,
                    ':categorie_id' => $categorie->parent_id,
                    ':element_id' => -1
                ));

                $foundedPath = $historyCategories[0]['path']; // Один путь мы уже включили при первой выборке
                foreach($historyCategoriesPlus as $categoriePlus) {
                    if($categoriePlus['path'] == $foundedPath) {
                        continue;
                    }
                    $historyCategories[] = $categoriePlus;
                }
            }
        }

		$categorieResult = array();
		if(count($historyCategories) > 0) {
            foreach($historyCategories as $categorie) {
                // Разные поля при разных выборках
                $categorieResult['id'] = $id !== false ? $id : $categorie['real_categorie_id'];
                $categorieResult['path'] = $path !== false ? $path : $categorie['path'];
                $categorieResult['undotted_path'] = implode('', explode('.', $categorieResult['path']));
                $categorieResult['name'] = $categorie['categorie_name'];
                $categorieResult['is_dynamic'] = $categorie['is_dynamic'];
                if($categorieResult['is_dynamic'] == 1) {
                    $categorieResult['pr_key'] = $categorie['medcard_id'].'|'.$categorie['greeting_id'].'|'.$categorie['path'].'|'.$categorie['categorie_id'].'|'.$id;
                }
                $parts = explode('.', $categorie['path']);
                // Если количество кусков и точек совпадает, то это неверно: в иерархии у этого элемента нет позиции
                if(mb_substr_count($categorie['path'], '.') == count($parts)) {
                    exit('Ошибка: категории c ID '.$categorie['categorie_id'].' не присвоена позиция в шаблоне!');
                }
                $parts = array_reverse($parts); // 1 с конца - номер элемента
                $categorieResult['position'] = $parts[0];
                $categorieResult['elements'] = array();

                // Для клонов вынимаем элементы из истории, а не из основной таблицы
                if($id && !$path) {
                    $elements = MedcardElement::model()->getElementsByCategorie($id);
                } else {
                    $elements = MedcardElementForPatient::model()->findAll(
                        'history_id = :history_id
                        AND greeting_id = :greeting_id
                        AND medcard_id = :medcard_id
                        AND categorie_id = :categorie_id
                        AND path LIKE :path
                        AND element_id != -1',
                        array(
                            ':greeting_id' => $this->greetingId,
                            ':medcard_id' => $this->medcard['card_number'],
                            ':history_id' => 1,
                            ':categorie_id' => $categorieResult['id'],
                            ':path' => $path.'%'
                        )
                    );
                }

                $numWrapped = 0; // Это число элементов, которые следуют за каким-то конкретным элементом
                foreach($elements as $key => $element) {
                    $elementResult = array();
                    // Проверим наличие элемента в истории, если это не выборка исторических элементов
                    if($id && !$path) {
                        $historyCategorieElement = $this->getHistoryElements('one', array(
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
			    $medcardCategorieElement->is_required = $element['is_required'];
                            $medcardCategorieElement->categorie_id = $categorieResult['id'];
                            $medcardCategorieElement->element_id = $element['id'];
                            $medcardCategorieElement->label_before = $element['label'];
                            $medcardCategorieElement->label_after = $element['label_after'];
                            $medcardCategorieElement->size = $element['size'];
                            $medcardCategorieElement->change_date = $this->currentDate;
                            $medcardCategorieElement->type = $element['type']; // У категории нет типа контрола
                            $medcardCategorieElement->guide_id = $element['guide_id'];
							$medcardCategorieElement->allow_add = $element['allow_add'];
                            $medcardCategorieElement->config = $element['config'];
                            if($element['default_value'] != null) {
                                $medcardCategorieElement->value = $element['default_value'];
                            }

                            if(!$medcardCategorieElement->save()) {
                                exit('Не могу перенести элемент из категории '.$categorieResult['id']);
                            }

                            $eCopy = $medcardCategorieElement;
                        } else {
                            $eCopy = $historyCategorieElement;
                        }
                        $elementResult['type'] = $eCopy->type;
                        $elementResult['label_before'] = $eCopy->label_before;
                        $elementResult['label_after'] = $eCopy->label_after;
						$elementResult['id'] = $eCopy->element_id;
                        $elementResult['guide_id'] = $eCopy->guide_id;
                        $elementResult['path'] = $eCopy->path;
						$elementResult['allow_add'] = $eCopy->allow_add;
						$pathParts = explode('.', $element['path']);
						$elementResult['position'] = array_pop($pathParts);
			$elementResult['is_required'] = $element['is_required'];
                        $elementResult['size'] = $element['size'];
                        $elementResult['is_wrapped'] = $element['is_wrapped'];
                        $elementResult['config'] = CJSON::decode($element['config']);
                    } else {
                        $elementResult['type'] = $element['type'];
                        $elementResult['label_before'] = $element['label_before'];
                        $elementResult['label_after'] = $element['label_after'];
                        $elementResult['id'] = $element['element_id'];
                        $elementResult['guide_id'] =  $element['guide_id'];
                        $elementResult['path'] = $element['path'];
						$elementResult['allow_add'] = $element['allow_add'];
						$pathParts = explode('.', $element['path']);
						$elementResult['position'] = array_pop($pathParts);
                        $elementResult['size'] = $element['size'];
                        $elementResult['is_wrapped'] = $element['is_wrapped'];
			$elementResult['is_required'] = $element['is_required'];
                        $elementResult['config'] = CJSON::decode($element['config']);
                    }

                    // Для выпадающих списков есть справочник
                    if(isset($elementResult['guide_id']) && $elementResult['guide_id'] != null) {
                        $medguideValuesModel = new MedcardGuideValue();
                        $medguideValues = $medguideValuesModel->getRows(false, $elementResult['guide_id']);
                        if(count($medguideValues) > 0) {
                            $guideValues = array();
                            foreach($medguideValues as $value) {
                                $guideValues[$value['id']] = $value['value'];
                            }
                            $elementResult['guide'] = $guideValues;
                        } else {
                            $elementResult['guide'] = array();
                        }
                    }

                    // Добавляем в форму
                    $elementResult['undotted_path'] = implode('|', explode('.', $elementResult['path']));
                    $this->formModel->setSafeRule('f'.$elementResult['undotted_path'].'_'.$elementResult['id']);
                    $this->formModel->setAttributeLabels('f'.$elementResult['undotted_path'].'_'.$elementResult['id'], $elementResult['label_before']);
                    $fieldName = 'f'.$elementResult['undotted_path'].'_'.$elementResult['id'];
                    $this->formModel->$fieldName = null;
                    $elementResult = $this->getFormValue($elementResult);

                    // Выясняем зависимости элемента. Для этого ориентируемся на значение
                    $elementResult = $this->getDependences($elementResult);

                    $numWrapped++;
                    if($elementResult['is_wrapped'] == 1) {
                        $elementResult['num_wraps'] = null;
                    } else {
                        $elementResult['num_wraps'] = $numWrapped;
                        $numWrapped = 0;
                    }

                    $categorieResult['elements'][] = $elementResult;
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
                if($id && !$path) {
                    $categoriesChildren = MedcardCategorie::model()->findAll('parent_id = :parent_id', array(':parent_id' => $id));
                // Дочерние элементы могут быть ещё и вложенными, поэтому выясняем ещё и в хистори, есть ли элементы, которые показать
                    $categoriesChildrenPlus = MedcardElementForPatient::model()->findAll(
                        'history_id = :history_id
                        AND greeting_id = :greeting_id
                        AND medcard_id = :medcard_id
                        AND categorie_id = :categorie_id
                        AND element_id = -1',
                        array(
                            ':greeting_id' => $this->greetingId,
                            ':medcard_id' => $this->medcard['card_number'],
                            ':history_id' => 1,
                            ':categorie_id' => $id
                        )
                    );

                    $numCategoriesChildren = count($categoriesChildren);
                    $numChildrenPlus = count($categoriesChildrenPlus);
                    for($i = 0; $i < $numChildrenPlus; $i++) {
                        for($j = 0; $j < $numCategoriesChildren; $j++) {
                            if($categoriesChildren[$j]->path != $categoriesChildrenPlus[$i]->path) {
                                $categoriesChildren[] = $categoriesChildrenPlus[$i];
                            }
                        }
                    }
                } else {
                    $categoriesChildren = MedcardElementForPatient::model()->findAll(
                        'categorie_id = :categorie_id
                        AND element_id = :element_id
                        AND greeting_id = :greeting_id
                        AND medcard_id = :medcard_id
                        AND history_id = :history_id
                        AND path LIKE :path',
                        array(
                            ':categorie_id' => $categorieResult['id'],
                            ':element_id' => -1,
                            ':greeting_id' => $this->greetingId,
                            ':medcard_id' => $this->medcard['card_number'],
                            ':history_id' => 1,
                            ':path' => $categorieResult['path'].'%'
                        )
                    );
                }

                if(count($categoriesChildren) > 0) {
                    // Дети есть. Для каждого из них вышеописанный процесс повторяется
                    $categorieResult['children'] = array();
                    foreach($categoriesChildren as $child) {
                        // Обычная категория
                        if(isset($child->id)) {
                            $categorieResult['children'][] = $this->getCategorie($child->id, $templateId, $templateName);
                        } else { // Категория-клон
                            $categorieResult['children'][] = $this->getCategorie(false, $templateId, $templateName, $child->path);
                        }
                    }
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
                $element['history_id_max'] = 1;
                return $element;
            } else {
                $historyId = $historyIdResult['history_id_max'];
                $element['history_id_max'] = $historyId;
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
            $fieldName = 'f'.$element['undotted_path'].'_'.$element['id'];
            // Если это комбо с множественным выбором
            if($element['type'] == 3) {
                $element['selected'] = array();
                $element['value'] = CJSON::decode($elementFinded['value']);
				if($element['value'] != null)
				{
					// Если $element['value'] - не массив, то считаем это один айдишник - 
					if(!is_array($element['value'])) {
						$element['selected'][$element['value']] = array('selected' => true);
					}
					else
					{
					// Иначе перебираем массив и записываем
						foreach($element['value'] as $id) {
                        		$element['selected'][$id] = array('selected' => true);
                    		}
					}
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

    public function drawCategorie($categorie, $form, $model, $lettersInPixel, $templatePrefix) {
        $this->render('CategorieElement', array(
            'categorie' => $categorie,
            'form' => $form,
            'model' => $model,
            'prefix' => $this->prefix,
            'canEditMedcard' => $this->canEditMedcard,
            'lettersInPixel' => $lettersInPixel,
            'templatePrefix' => $templatePrefix
        ));
    }

    public function getFieldsHistoryByDate($date, $medcardId) {
        $this->formModel = new FormTemplateDefault();
        $this->historyElements = MedcardElementForPatient::model()->getValuesByDate($date, $medcardId);
        // Теперь это говно нужно рекурсивно разобрать и построить по шаблону
        $this->makeTree();
        // Теперь поделим категории
        $this->divideTreebyCats();
        // Рассортируем
        $this->sortTree();
	//var_dump($this->dividedCats);
	//exit();
        $result = $this->render('application.modules.doctors.components.widgets.views.HistoryTree', array(
            'categories' => $this->historyTree,
            'model' => $this->formModel,
            'templates' => $this->catsByTemplates,
            'dividedCats' => $this->dividedCats,
            'lettersInPixel' => Setting::model()->find('module_id = -1 AND name = :name', array(':name' => 'lettersInPixel'))->value,
        ), true);
        return $result;
    }

    public function divideTreebyCats() {
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

                    $this->dividedCats[$templateId]['cats'][$id] = $this->historyTree[$id];
                }
            }
        }
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
                        $node = array(
                            'elements' => array(),
                            'children' => array()
                        );
                        $currentNode[$pathArr[$i]] = $node;
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
                                'name' => $element['categorie_name'],
                                'path' => implode('-', explode('.', $element['path'])),
                                'children' => array(),
                                'elements' => array(),
                            );
                            $currentNode[$pathArr[$i]] = $node;
                        } else {
                            $currentNode[$pathArr[$i]]['name'] = $element['categorie_name'];
                            $currentNode[$pathArr[$i]]['path'] = implode('-', explode('.', $element['path']));
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
                            'guide_id' => $element['guide_id'],
                            'path' => $element['path'],
                            'config' => CJSON::decode($element['config'])
                        );

                        if($element['guide_id'] != null) {
                            $medguideValuesModel = new MedcardGuideValue();
                            $medguideValues = $medguideValuesModel->getRows(false, $element['guide_id']);
                            // Проинициализируем пустым массивом массив значений
			    $data['guide'] = array();
			    if(count($medguideValues) > 0) {
                                // Если не список множественного выбора
				if($data['type'] != 3)
				{
				    $guideValues = array();
				    foreach($medguideValues as $value) {
				        $guideValues[$value['id']] = $value['value'];
				    }
				    $data['guide'] = $guideValues;
				}
                            }
				
			    /*
			    if(count($medguideValues) > 0) {
                                $guideValues = array();
                                foreach($medguideValues as $value) {
                                    $guideValues[$value['id']] = $value['value'];
                                }
				// Если не список множественного выбора
				//if($data['type'] != 3)
				    $data['guide'] = $guideValues;
                            }*/
			    
                           if($data['type'] == 3) {
                                $data['selected'] = array();
                                $data['value'] = CJSON::decode($element['value']);
                                if($data['value'] != null) {
				    if (is_array($data['value']))
				    {
					foreach($data['value'] as $id) {
					    $data['selected'][$id] = array('selected' => true);
					    //$data['guide'][$id] = $medguideValues[$id];
					    // Перебираем массив $medguideValues и в случае, если выбранное значение
					    //   равно значению из $medguideValues, то добавляем в $data['guide']
					    //     значение из справочника
					    foreach ($medguideValues as $value)
					    {
					        if ($value['id']==$id)
					        {
						    $data['guide'][$value['id']] = $value['value'];
					        }
					    }
					
					}
				    }
				    else
				    {
					$data['selected'][$data['value']] = array('selected' => true);
					foreach ($medguideValues as $value)
					{
					    if ($value['id']==$data['value'])
					    {
						$data['guide'][$value['id']] = $value['value'];
					    }
					}
				    }
                                } else {
                                    $data['selected'] = array();
                                }
                            }
                            // Простой выпадающий список
                            if($data['type'] == 2) {
                                $data['selected'] = array($data['value'] => array('selected' => true));
                            }
                        }
                        $data = $this->getDependences($data);

                        $currentNode['elements'][] = $data;

                        $this->formModel->setSafeRule('f'.$element['element_id']);
                        $this->formModel->setAttributeLabels('f'.$element['element_id'], $element['label_before']);
                        $fieldName = 'f'.$element['element_id'];
                        $this->formModel->$fieldName = null;
                    }
                }
            }
        }

        //ksort($this->historyTree);
        //$this->sortTree();
        //var_dump($this->historyTree);
        //exit();
    }

    private function sortTree(&$node = false) {
        if(!$node) {
            $node = $this->dividedCats;
        }

        if(isset($node['cats'])) {
            foreach($node['cats'] as &$cat) {
                usort($cat['elements'], function($element1, $element2) {
                    $position1 = array_pop(explode('.', $element1['path']));
                    $position2 = array_pop(explode('.', $element2['path']));

                    if($position1 > $position2) {
                        return 1;
                    } elseif($position1 < $position2) {
                        return -1;
                    } else {
                        return 0;
                    }
                });

                if(isset($cat['children']) && count($cat['children']) > 0) {
                    $this->sortTree($cat['children']);
                }
            }
        }
    }

    public function drawHistoryCategorie($categorie, $cId, $form, $model, $prefix = false, $templateKey, $lettersInPixel) {
        if($prefix === false) {
            $prefix = $this->prefix;
        }
        $this->render('HistoryTreeElement', array(
            'categorie' => $categorie,
            'prefix' => $prefix,
            'form' => $form,
            'model' => $model,
            'cId' => $cId,
            'templateKey' => $templateKey,
            'lettersInPixel' => $lettersInPixel
        ));
    }

    private function getDependences($element) {
        $element['dependences'] = array();
        // Элемент участвует в зависимостях как тот, от кого зависят
        // Попробуем найти его в истории зависимостей
        $actorsHistory = MedcardElementPatientDependence::model()->findAll(
            'element_path = :element_path
            AND medcard_id = :medcard_id
            AND greeting_id = :greeting_id',
            array(
                ':element_path' => $element['path'],
                ':greeting_id' => $this->greetingId,
                ':medcard_id' => $this->medcard['card_number'],
            )
        );
        // Если нет отметок зависимостях, то добавить новые: это означает, что шаблон запускается первый раз
        if(count($actorsHistory) == 0) {
            $actors =  MedcardElementDependence::model()->findAll('
                element_id = :element_id', array(
                ':element_id' => $element['id']
            ));
            foreach($actors as $actor) {
                $dependenceModel = new MedcardElementPatientDependence();
                $dependenceModel->element_path = $element['path'];
                $dependenceModel->action = $actor['action'];
                $dependenceModel->medcard_id = $this->medcard['card_number'];
                $dependenceModel->greeting_id = $this->greetingId;
                $dependenceModel->value = $actor['value_id'];
                $dependenceModel->dep_element_id = $actor['dep_element_id'];
                $dependenceModel->element_id = $element['id'];

                // Чтобы узнать путь зависимого элемента, его надо выбрать
                $depElementModel = MedcardElement::model()->findByPk($actor['dep_element_id']);
                if($depElementModel == null) {
                    continue;
                }
                $dependenceModel->dep_element_path = $depElementModel->path;

                if(!$dependenceModel->save()) {
                    exit('Не могу сохранить зависимости для элемента '.$element['path']);
                }
                $actorsHistory[] = $dependenceModel;
            }
        }

        $element['dependences'] = array(
            'list' => array()
        );

        // Смотрим, не установлено ли значение элемента, от которого зависит данный элемент. Для этого нужно выяснить, от кого зависит данный элемент. Смотрим: если в хистори не занесено, от кого зависит данный элемент, значит это первая генерация шаблона, и можно взять из актуальных зависимостей
        // Выбираем всё, от чего зависит данный элемент
        /*
         Если мы говорим о том, что какой-то элемент нужно показать, то по умолчанию он скрыт. Если мы говорим, что какой-то элемент         нужно скрыть при определённом значении, то, значит, по умолчанию его надо скрыть
        */
        foreach($actorsHistory as $actorHistory) {
            $element['dependences']['list'][] = array(
                'action' => $actorHistory['action'],
                'value' => $actorHistory['value'],
                'path' => $actorHistory['dep_element_path'],
                'elementId' => $actorHistory['dep_element_id']
            );
        }

        return $element;
    }
}
?>