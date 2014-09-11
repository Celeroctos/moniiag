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
	private $historyElements = array(); // Массив истории элементов: по ним воссоздаётся шаблон
    private $maxRecordIdByTemplateGreeting = -1;
    public $historyTree = array(); // Построенное дерево историии
    public $catsByTemplates = array(); // Категории по шаблону
    public $dividedCats = array(); // Поделённые категории
    public $templatePrefix = null;
    public $templateId = null; // Это айдишник шаблона
    public $templateName = null; // Это название шаблона
    public $medcardRecordId = null;
    public $previewMode = false;
    public $isActiveTemplate = null; // Флаг активного шаблона (активной вкладки),
	public $form = null;
    public $model = null;

    public function run() {
        ini_set('max_execution_time', 60);
        $this->createFormModel();
        // Найдём имя шаблона
        if (isset($this->templateId))
        {
            $this->templateName = MedcardTemplate::model()->findByPk($this->templateId);
            $this->templateName = $this->templateName['name'];

            // А если ещё и id приёма - то ищем максимальный record_id для данного приёма для данного шаблона
            if(is_null($this->greetingId)==false)
            {
                $this->maxRecordIdByTemplateGreeting = MedcardRecord::getMaxRecIdOnGreeting(
                    $this->templateId,$this->greetingId
                );
            }

        }

        if($this->currentDate == null) {
            $this->currentDate = date('Y-m-d h:i');
        }
        // Категории нужны, чтобы сформировать первичный шаблон для пациента в том случае, когда у пациента нет перенесённых записей о данном приёме в хистори. Для начала проверим, есть ли шаблон приёма. Если нет - вынимаем категории и помещаем их в историю
        // Т.е. в приём не вносили изменений, шаблона истории нет
        if(($this->greetingId == null || $this->medcard == null) && !$this->previewMode) {
            $categories = array();
        } else {		
			$categories = $this->getCategories($this->templateType, $this->medcardRecordId,$this->templateId);
        }
		/*
		//
		echo('--------');
		var_dump($categories);
		
		echo('--------');
		exit();
		//
        */
/*
        //
        echo('--------');
        var_dump($this->formModel);

        echo('--------');
        exit();
        //*/
        //echo '<pre>';
        //var_dump($categories);
        //exit();
        $answer = $this->render('application.modules.doctors.components.widgets.views.CategorieViewWidget', array(
            'categories' => $categories,
            'model' => $this->formModel,
			'form' => $this->form,
            'currentPatient' => $this->currentPatient,
            'greetingId' => $this->greetingId,
            'greeting' => ($this->greetingId != null) ? SheduleByDay::model()->findByPk($this->greetingId) : null,
            'withoutSave' => $this->withoutSave,
            'lettersInPixel' => Setting::model()->find('module_id = -1 AND name = :name', array(':name' => 'lettersInPixel'))->value,
            'canEditMedcard' => $this->canEditMedcard,
            'templatePrefix' => $this->templatePrefix,
			'currentRecordId' => $this->medcardRecordId,
            'previewMode' => $this->previewMode,
            'isActiveTemplate' => $this->isActiveTemplate,
            'templateId' => $this->templateId,
			'templateType' => $this->templateType,
            'templateName' => $this->templateName,
        ), true);
        if(!Yii::app()->request->isAjaxRequest) {
            echo $answer;
        } else {
            return $answer;
        }
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
	public function getCategories($pageId, $recordId, $templateId = null) {
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

                if(!$this->previewMode) {
                    $historyCategorie = $this->getHistoryElements('one', array(
                        ':greeting_id' => $this->greetingId,
                        ':medcard_id' => $this->medcard['card_number'],
                        ':history_id' => 1,
                        ':path' => $categorie->path
                    ));
                } else {
                    $historyCategorie = null;
                }

                if($historyCategorie == null) {
					$categorieResult = $this->getCategorie($id, $recordId, $template['id'], $template['name']);
                } else {
					$categorieResult = $this->getCategorie(false, $recordId, $template['id'], $template['name'], $historyCategorie->path);
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
       // echo "<pre>";
		//var_dump($categoriesResult);
       // exit();
        return $categoriesResult;
    }

	public function setHistoryElements($newHistoryElements)
	{
		$this->historyElements = $newHistoryElements;
	}

	public function getCategorie($id = false, $recordId, $templateId, $templateName, $path = false) {
		// Выбираем категорию
        if($id && !$path) { // Категории, для которых делается выборка по-первому
            $categorie = MedcardCategorie::model()->findByPk($id);
            if($categorie == null) {
                return array();
            }
            if(!$this->previewMode) {
                $historyCategories = $this->getHistoryElements('multiple',  array(
                    ':greeting_id' => $this->greetingId,
                    ':medcard_id' => $this->medcard['card_number'],
                    ':history_id' => 1,
                    ':categorie_id' => $categorie->parent_id,
                    ':path' => $categorie->path
                ));
            } else {
                $historyCategories = array();
            }
        } else {
            if(!$this->previewMode) {
                $historyCategories = $this->getHistoryElements('multiple',  array(
                    ':greeting_id' => $this->greetingId,
                    ':medcard_id' => $this->medcard['card_number'],
                    ':history_id' => 1,
                    ':path' => $path
                ));
            } else {
                $historyCategories = array();
            }
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
            if(!$this->previewMode) {
                $medcardCategorie->medcard_id = $this->medcard['card_number'];
                $medcardCategorie->greeting_id = $this->greetingId;
            }
            $medcardCategorie->history_id = 1;
			$medcardCategorie->template_page_id = $this->templateType;
			$medcardCategorie->record_id = $recordId;
            $medcardCategorie->categorie_name = $categorie->name;
	   // $medcardCategorie->is_required = $categorie->is_required;
            $medcardCategorie->path = $categorie->path;
            $medcardCategorie->is_wrapped = 0;
            $medcardCategorie->categorie_id = $categorie->parent_id;
            $medcardCategorie->element_id = -1;
            //$medcardCategorie->change_date = $this->currentDate;
			$medcardCategorie->change_date = date('Y-m-d H:i');
			$medcardCategorie->type = -1; // У категории нет типа контрола
            $medcardCategorie->template_id = $templateId;
            $medcardCategorie->template_name = $templateName;
            $medcardCategorie->is_dynamic = $categorie->is_dynamic;
            $medcardCategorie->real_categorie_id = $categorie->id;
            $medcardCategorie->config = CJSON::encode(array(
                'isWrapped' => $categorie->is_wrapped
            ));

            // Сохраняем только в том случае, если это не previewMode
            if(!$this->previewMode && !$medcardCategorie->save()) {
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
                    $categorieResult['pr_key'] = $categorie['medcard_id'].'|'.$categorie['greeting_id'].'|'.$categorie['path'].'|'.$categorie['categorie_id'].'|'.$categorieResult['id'];
                }
                $parts = explode('.', $categorie['path']);
                // Если количество кусков и точек совпадает, то это неверно: в иерархии у этого элемента нет позиции
                if(mb_substr_count($categorie['path'], '.') == count($parts)) {
                    exit('Ошибка: категории c ID '.$categorie['categorie_id'].' не присвоена позиция в шаблоне!');
                }
                $parts = array_reverse($parts); // 1 с конца - номер элемента
                $categorieResult['position'] = $parts[0];
                $categorieResult['elements'] = array();
                $categorieResult['config'] = CJSON::decode($categorie['config']);

                // Для клонов вынимаем элементы из истории, а не из основной таблицы
                if(($id && !$path) || $this->previewMode) {
                    $elements = MedcardElement::model()->getElementsByCategorie($id);
                } else {
                    // Здесь выбираются ЭЛЕМЕНТЫ (без категорий), если приём был начат раньше
                    $elements = array();
                    // Проверяем - если maxRecordIdByTemplateGreeting равно нулю, то присваиваем ему значение 1
                    if (is_null($this->maxRecordIdByTemplateGreeting))
                    {
                        $this->maxRecordIdByTemplateGreeting = 1;
                    }
                    $mepObject = new MedcardElementForPatient();
                    $elements = $mepObject->findElementsForGreeting(
                        $this->greetingId,
                        $this->medcard['card_number'],
                        $categorieResult['id'],
                        $path);
                }

                $numWrapped = 0; // Это число элементов, которые следуют за каким-то конкретным элементом
                foreach($elements as $key => $element) {
                    $elementResult = array();
                    // Проверим наличие элемента в истории, если это не выборка исторических элементов
                    if($id && !$path && !$this->previewMode) {
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
							$medcardCategorieElement->template_page_id = $this->templateType;
							$medcardCategorieElement->record_id = $recordId; 
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
                            //$medcardCategorieElement->change_date = $this->currentDate;
							$medcardCategorieElement->change_date = date('Y-m-d H:i');
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
                        $elementResult['label_before'] = isset($element['label']) ? $element['label'] : $element['label_before'];
                        $elementResult['label_after'] = $element['label_after'];
                        $elementResult['id'] = isset($element['id']) ? $element['id'] : $element['element_id'];
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
                        $medguideValues = $medguideValuesModel->getRows(false, $elementResult['guide_id'], 'value', 'asc',  false, false, $elementResult['path'], $this->greetingId);
                        //var_dump($medguideValues);
                        $guideValues = array();
						if(count($medguideValues) > 0) {
                           // var_dump($medguideValues);
                            $guideValues = array();
                            foreach($medguideValues as $value) {
                                // Если значение из справочника равно "-3" и тип не равен 7ми, то добавим значение
                               // if (!(($value['id']==-3)&& ($elementResult['type']==7)))
                                //{
                                    //var_dump("!");
                                    //var_dump($value['value']);
                                    $guideValues[$value['id']] = $value['value'];
                                //}
                            }
                            $elementResult['guide'] = $guideValues;
                        } else {
                            $elementResult['guide'] = array();
                        }
                        //var_dump($elementResult['guide']);
                        //echo ("|");

                    }

                    // Добавляем в форму
                    $elementResult['undotted_path'] = implode('|', explode('.', $elementResult['path']));
                    $this->formModel->setSafeRule('f'.$elementResult['undotted_path'].'_'.$elementResult['id']);
                    if($this->previewMode) {
                        /* $parts = explode('.', $elementResult['path']);
                        $parts[count($parts) - 1] = '<strong class="text-danger">'.$parts[count($parts) - 1].'</strong>';
                        $displayedPath = implode('.', $parts);
                       $elementResult['label_before'] = '<strong class="text-danger bold">ID '.$elementResult['id'].'</strong>, <strong class="text-primary bold">путь '.$displayedPath.'</strong>, '.$elementResult['label_before']; */
                    }
                    $this->formModel->setAttributeLabels('f'.$elementResult['undotted_path'].'_'.$elementResult['id'], $elementResult['label_before']);
                    $fieldName = 'f'.$elementResult['undotted_path'].'_'.$elementResult['id'];
                    $this->formModel->$fieldName = null;
                    if(!$this->previewMode) {
                        $elementResult = $this->getFormValue($elementResult);
                    } else {
                        $elementResult['selected'] = array();
                    }

                    // Выясняем зависимости элемента. Для этого ориентируемся на значение
                    $elementResult = $this->getDependences($elementResult);

                    $numWrapped++;
                    if($elementResult['is_wrapped'] == 1) {
                        $elementResult['num_wraps'] = null;
                    } else {
                        $elementResult['num_wraps'] = $numWrapped;
                        $numWrapped = 0;
                    }
                    //var_dump($elementResult);
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
                    if(!$this->previewMode) {
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
                    } else {
                        $categoriesChildrenPlus = array();
                    }

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
                    if(!$this->previewMode) {

                       //var_dump('!');
                       // exit();

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
                                ':path' => $categorieResult['path'].'.%'
                            )
                        );



                    }
                }
				$categorieResult['children'] = array();
                if(count($categoriesChildren) > 0) {
                    // Дети есть. Для каждого из них вышеописанный процесс повторяется
                  
                    foreach($categoriesChildren as $child) {
                        // Обычная категория
                        if(isset($child->id)) {
							$categorieResult['children'][] = $this->getCategorie($child->id, $recordId, $templateId, $templateName);
                        } else { // Категория-клон
							$categorieResult['children'][] = $this->getCategorie(false, $recordId, $templateId, $templateName, $child->path);
                        }
                    }
                }
            }

		}
		// Имеем два массива - children, с категориями-детьми. 
		//    И массив elements - c элементами-детьми
		//   Создаём специальный массив, каждый элемент которого будет содержать следующее:
		//    - Номер массива (children/elements)
		//    - Значение поля "Позиция в родителе"
		//    - Номер в массиве children или elements
		$itemsOrders = array();
		for($i=0;$i<count($categorieResult['children']);$i++)
		{
			$itemsOrders[] = array(
				'arrayNumber' => '1',
				'position' => $categorieResult['children'][$i]['position'],
				'numberInArray' => $i
			);
		}

		for($i=0;$i<count($categorieResult['elements']);$i++)
		{
			$itemsOrders[] = array(
				'arrayNumber' => '2',
				'position' => $categorieResult['elements'][$i]['position'],
				'numberInArray' => $i
			);
		}
		
	    // Сортируем массив itemsOrder по элементу position
	    usort($itemsOrders, function($element1, $element2) {
            if($element1['position'] > $element2['position']) {
                return 1;
            } elseif($element1['position'] < $element2['position']) {
                return -1;
            } else {
                return 0;
            }
        });
		
		
		$categorieResult['childrenElementsOrder'] = $itemsOrders;
		//var_dump($categorieResult['childrenElementsOrder']);
		
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
                if($element['type'] == 3 || $element['type'] == 2 || $element['type'] == 7) {

                   /* if ($element['id']==339)
                    {
                        var_dump("!");
                        exit();
                    }*/

                    $element['selected'] = array();
                }
                $element['history_id_max'] = 1;
                return $element;
            } else {
                $historyId = $historyIdResult['history_id_max'];
                $element['history_id_max'] = $historyId;
            }
        }
       /* if ($element['id']==339)
        {
            var_dump("!");
            exit();
        }*/
        // Делаем выборку из базы значения
        $elementFinded = MedcardElementForPatient::model()->find(
            'element_id = :element_id
             AND medcard_id = :medcard_id
             AND history_id = :history_id
             AND path = :element_path',
            array(':medcard_id' => $medcardId,
                  ':element_id' => $element['id'],
                  ':history_id' => $historyId,
                  ':element_path' => $element['path']
                 )
        );
        /*if ($element['id']==339)
        {
            var_dump($elementFinded);
            exit();
        }*/
        if($elementFinded != null) {
            /*if ($element['id']==339)
            {
                var_dump("!");
                exit();
            }*/
            $fieldName = 'f'.$element['undotted_path'].'_'.$element['id'];
            // Если это комбо с множественным выбором или двухколоночный список
            if($element['type'] == 3 || $element['type'] == 7) {
                /*if ($element['id']==339)
                {
                    var_dump("!");
                    exit();
                }*/
                $element['selected'] = array();
                $element['value'] = CJSON::decode($elementFinded['value']);

               /*if ($element['id']==339)
               {
                   var_dump($elementFinded['value']);
                   exit();
               }*/
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

	public function getFieldsHistoryByDate($medcardId,$greetingId,$templateId) {
        $this->formModel = new FormTemplateDefault();
		$this->historyElements = MedcardElementForPatient::model()->getValuesByDate($medcardId,$greetingId,$templateId);
        // Теперь это говно нужно рекурсивно разобрать и построить по шаблону
		//var_dump('sdfgsdfzxd');
       // echo '<pre>';
       // var_dump($this->historyElements);
		//exit();
		
		$this->makeTree('getTreeNode');
        $this->sortTree();
       //var_dump($this->historyTree);
        //exit();
        // Теперь поделим категории
        $this->divideTreebyCats();
       /* var_dump('3cvgfbhdhgarc');
         echo '<pre>' ;var_dump($this->dividedCats);
        exit();*/
		$greeting  = null;
        // Рассортируем
		// Вытащим приёмы
		// Берём элементы истории и прочитываем id приёма
		if (count($this->historyElements)>0)
		{
			for ($i = 0;$i<count($this->historyElements);$i++)
			{
				if 	($this->historyElements[$i]['element_id']!=-1)
				{
					$greeting = $this->historyElements[$i]['greeting_id'];	
					break;	
				}
			}
		}
		//var_dump($greeting);
		//exit();
		if ($greeting!=NULL)
		{
			$pd = PatientDiagnosis::model()->findDiagnosis($greeting, 0);
			$sd = PatientDiagnosis::model()->findDiagnosis($greeting, 1);
            $cd = PatientDiagnosis::model()->findDiagnosis($greeting, 2);
            $cpd = ClinicalPatientDiagnosis::model()->findDiagnosis($greeting, 0);
			$csd = ClinicalPatientDiagnosis::model()->findDiagnosis($greeting, 1);
            $greetingObject = SheduleByDay::model()->findByPk($greeting);
            $noteDiagnosis = $greetingObject ['note'];

		}
		else
		{
			$pd = array();
			$sd = array();
            $cd = array();
			$cpd = array();
			$csd = array();
            $noteDiagnosis = '';
		}
        $result = $this->render('application.modules.doctors.components.widgets.views.HistoryTree', array(
            'categories' => $this->historyTree,
            'primaryDiagnosis' => $pd,
            'secondaryDiagnosis' => $sd,
            'complicating' => $cd,
			'clinicalPrimaryDiagnosis' => $cpd,
			'clinicalSecondaryDiagnosis' => $csd,
            'noteDiagnosis' => $noteDiagnosis,
            'model' => $this->formModel,
            'templates' => $this->catsByTemplates,
            'dividedCats' => $this->dividedCats,
            'lettersInPixel' => Setting::model()->find('module_id = -1 AND name = :name', array(':name' => 'lettersInPixel'))->value,
        ), true);
        return $result;
    }

    public function divideTreebyCats() {
    	
    	$templates = array();
    	// Перебираем верхний уровень дерева
    	
		//var_dump($this->historyTree);
		//exit();
		$num = 0;
        //var_dump($this->historyTree);
        //exit();
    	foreach ($this->historyTree as $nodeTopLevel)
    	{
			// Возьмём элемент массива element и прочитаем у 
			//     него template_name и templateid
			$tName = $nodeTopLevel['element']['template_name'];
			$tId = $nodeTopLevel['element']['template_id'];



			// Если в templates нет ИД шаблона - добавляем
			if (!isset($templates[$tId]))
			{
				$templates[$tId] = array(
					'name' => $tName,
					'cats' => array()
				);

                // Если у верхнего элемента есть template_page_id - берём его в шаблон
                if (isset($nodeTopLevel['element']['template_page_id']))
                {
                    $templates[$tId]['template_page_id'] = $nodeTopLevel['element']['template_page_id'];
                }

			}
			// Теперь добавим категорию в соответвующий шаблон
			$templates[$tId]['cats'][] = $nodeTopLevel;
		}
		//var_dump($num);
		//exit();
    	// В dividedCats - добавляем собранные категории
        //----->
        //  После распределения шаблонов пробегаемся по шаблонам, внутри пробегаемся по категориям верхнего уровня,
        //     смотрим - есть ли в шаблоне хотя бы одна не пустая категория
        //    Если её нет - то удаляем шаблон из вывода
        $keysTpDelete = array();
        foreach ($templates as $templateKey => &$oneTemplate)
        {
            $isNotEmptyCategory = false;
            foreach ($oneTemplate['cats'] as $categoryKey => $oneCategory)
            {

                // Если категория не пуста - поднимаем флаг $isNotEmptyCategory
                //var_dump($oneCategory['element']['empty']);
                if ($oneCategory['element']['empty']==false)
                {

                    $isNotEmptyCategory = true;
                    // Дальше не имеет смысла прогонять цикл
                    break;
                }
            }

            if ($isNotEmptyCategory)
            {
                //var_dump('!');
                //exit();
                // Ставим флаг "empty" = false
              //  var_dump('1');
                $oneTemplate['empty'] = false;
            }
            // Иначе "empty" = true
            else
            {
               // var_dump('2');
                $oneTemplate['empty'] = true;
            }

        }
//exit();
    	$this->dividedCats = $templates;
     //   var_dump($templates);
      //  exit();
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

	// Функция принимает элмент истории и делает из него узел дерева
	private function getTreeNode($historyElement)
	{
		$nodeContent = array();
		if ($historyElement['element_id'] == -1)
		{
			$nodeContent['name'] = $historyElement['categorie_name'];
			$nodeContent['template_id'] = $historyElement['template_id'];
			$nodeContent['template_name'] = $historyElement['template_name'];
			$nodeContent['path'] = $historyElement['path'];
			$nodeContent['element_id'] = -1;
            $nodeContent['empty'] = true;

        }
		else
		{
			$nodeContent = array(
                           'label' => $historyElement['label_before'],
                            'label_after' => $historyElement['label_after'],
                            'size' => $historyElement['size'],
                            'is_wrapped' => $historyElement['is_wrapped'],
                            'value' => $historyElement['value'],
                            'id' => $historyElement['element_id'],
                            'element_id' => $historyElement['element_id'],
                            'type' => $historyElement['type'],
                            'guide_id' => $historyElement['guide_id'],
                            'path' => $historyElement['path'],
                            'config' => CJSON::decode($historyElement['config'])	
                        );
             // Дальше идёт трэш по инициализации значений контролов
			//----------
			    if($nodeContent['guide_id'] != null)
                        {
                            $medguideValuesModel = new MedcardGuideValue();
                            $medguideValues = $medguideValuesModel->getRows(false, $nodeContent['guide_id'], 'value', 'asc', false, false, $nodeContent['path'], $historyElement['greeting_id']);
                            // Проинициализируем пустым массивом массив значений
			    			$nodeContent['guide'] = array();
			    			if(count($medguideValues) > 0)
			    			{
                                // Если не список множественного выбора
								if($nodeContent['type'] != 3 && $nodeContent['type'] != 7)
								{
				    				$guideValues = array();
				    				foreach($medguideValues as $value)
				    				{
				        				$guideValues[$value['id']] = 
				        				$value['value'];
				    				}
				    				$nodeContent['guide'] = $guideValues;
								}
                            }
                            if($nodeContent['type'] == 3 || $nodeContent['type'] == 7)
                            {
                                $nodeContent['selected'] = array();
                                $nodeContent['value'] = 
                                CJSON::decode($nodeContent['value']);
                                if($nodeContent['value'] != null)
                                {
				    				if (is_array($nodeContent['value']))
				    				{
										foreach($nodeContent['value'] as $id)
										{
					    					$nodeContent['selected'][$id] = 
					    						array('selected' => true);
					    
					    					foreach ($medguideValues as $value)
					    					{
					        					if ($value['id']==$id)
					        					{
						    						$nodeContent['guide']
						    						[$value['id']] 
						    							= $value['value'];
					        					}
					    					}
					
										}
				    				}
				    				else
				    				{
										$nodeContent['selected'][$nodeContent['value']] 
											= array('selected' => true);
										foreach ($medguideValues as $value)
										{
					    					if ($value['id']==$nodeContent['value'])
					    					{
												$nodeContent['guide']
												[$value['id']] = 
													$value['value'];
					    					}
										}
				    				}


                                } 
                                else
                                {
                                    $nodeContent['selected'] = array();
                                }


                            }
                            // Простой выпадающий список
                            if($nodeContent['type'] == 2)
                            {
								if(!isset($nodeContent['value']) || $nodeContent['value'] == null) {
									$nodeContent['selected'] = array();
								} else {
									$nodeContent['selected'] = array($nodeContent['value'] => array('selected' => true));
								}
                            }
                        }
                        
                        $nodeContent = $this->getDependences($nodeContent);
                        
						$this->formModel->setSafeRule('f'.$historyElement['element_id']);
                        $this->formModel->setAttributeLabels('f'.$historyElement['element_id'], $historyElement['label_before']);
                        $fieldName = 'f'.$historyElement['element_id'];
                        $this->formModel->$fieldName = null;
						//------
		}
		
		//if (!isset($nodeContent['path']))
		//	{
				//var_dump($historyElement);
				//exit();
		//	}
		return $nodeContent;
	}


	private function getTreeNodePrint($historyElement)
	{
		$nodeContent = array();
        //var_dump("!");
        //exit();
        //var_dump($historyElement);
		//exit();
        if ($historyElement['element_id'] == -1)
		{
           // var_dump("!");
           // exit();
            //var_dump($historyElement);
            //exit();
			$nodeContent['name'] = $historyElement['categorie_name'];
			$nodeContent['template_id'] = $historyElement['template_id'];
			$nodeContent['template_name'] = $historyElement['template_name'];
            $nodeContent['template_page_id'] = $historyElement['template_page_id'];
            $nodeContent['element_id'] = -1;
            $nodeContent['empty'] = true;

        }
		else
		{
			$nodeContent = array(
                           'label' => $historyElement['label_before'],
                            'label_after' => $historyElement['label_after'],
							'value' => $historyElement['value'],
                            'id' => $historyElement['element_id'],
                            'element_id' => $historyElement['element_id'],
                            'path' => $historyElement['path'],
							'config' => $historyElement['config'],
							'type' => $historyElement['type'],
				'is_wrapped' => $historyElement['is_wrapped'],
				//'info' => $historyElement['info'],
                            'config' => CJSON::decode($historyElement['config'])	
                        );
				if($historyElement['guide_id'] != null)
                {			
							// Если тип - таблица 
							if ($historyElement['type']==4)
							{
								$nodeContent['value'] = 
										CJSON::decode($nodeContent['value']);
							}
							// Если тип - выпадающий список или список множественного выбора
							if ($historyElement['type']==3 ||$historyElement['type']==2 || $historyElement['type']==7)
							{
								$medguideValuesModel = new MedcardGuideValue();
								$medguideValues = $medguideValuesModel->getRows(false, $historyElement['guide_id'], 'value', 'asc', false, false, $nodeContent['path'], $historyElement['greeting_id']);
								if ($historyElement['type']==3 ||$historyElement['type']==7 )
								{
									$nodeContent['value'] = 
										CJSON::decode($nodeContent['value']);

								}
								// Прочитываем значения и транслируем список id-шников в строку со значениями, разделёнными запятой
								if (is_array($nodeContent['value']))
								{
						
									// Перебираем 
									$newElementValue = '';
									foreach($nodeContent['value'] as $id)
									{
										foreach($medguideValues as $value)
										{
											if ($value['id']==$id)
											{
												if ($newElementValue!='')
												{
													$newElementValue .= ', ';
												}
												$newElementValue .= $value['value'];
											}
										}
							
									}
									$nodeContent['value'] = $newElementValue;
								}
								else
								{
									foreach($medguideValues as $value)
									{
										if($value['id']==$nodeContent['value'])
										{
											$nodeContent['value'] = $value['value'];
										}
									}
								}
							}
                        }
		}
		return $nodeContent;
	}

	// Возвращает конкатенирует части адреса из массива до позиции, указанной в pointer
	private function getSubPath($pathArray, $pointer)
	{
		$result = '';
		
		for ($i=0;$i<$pointer;$i++)
		{
			$result .= $pathArray[$i];
			if ($i!=($pointer-1))
			{
					$result .= '.';
			}	
		}
		
		return $result;
	}

	// Ищет в элементах истории тот, который находится по пути pathArray c ограничителем pointer
	//     (максимальный индекс<pointer-а)
	private function getTreeNodeCategory($pathArray, $pointer)
	{
        //var_dump($pathArray);
        //var_dump($pointer);
        //exit();

		$result = false;
		$pathToFind = $this->getSubPath($pathArray, $pointer);
		//var_dump($pathToFind);
		//exit();
        foreach($this->historyElements as $element) {
            // Равны ли пути
            if (strcmp($element['path'],$pathToFind)==0)
			{
				$result = 	$element;
				break;
			}
		}
		return $result;
	}

	public function makeTree($getTreeNodeFunction) {
        //var_dump($this->historyElements);
        //exit();
		foreach($this->historyElements as $element) {
			//var_dump($element);
            //exit();
			// Перебираем только элементы - категории добавляем, по требованию
			if ($element['element_id']!=-1)
			{
				// Если свойство NULL или пустое значение - берём следующий элемент
				if ($element['value']=='' || $element['value']==NULL)
                {

                   //exit();
					continue;
                }
                else
                {
                    // Попытаемся преобразовать value JSON-объект
                    try
                    {
                        $JSONObject = CJSON::decode($element['value']);
                        // Если объект - массив и он пустой, то
                        if (is_array($JSONObject))
                        {
                            if (count($JSONObject)==0)
                            {
                                continue;
                            }
                        }

                    }
                    catch (Exception $e)
                    {

                    }
                }
                //var_dump("!");
                //exit();
               // var_dump($element['value']/*. ' ' . $element['label_before']*/);
				// Делим путь 
				$pathArr = explode('.', $element['path']);
				$currentResultTree = &$this->historyTree;
				// Перебираем куски адреса  с каждым куском адреса 
				//     перемещаем указатель на текущий узел


				for ($i=0;$i<count($pathArr)-1;$i++)
				{
					// Если в узле нет такого ключа - создаём его
					if (!isset($currentResultTree[$pathArr[$i]]))
					{
						$currentResultTree[$pathArr[$i]] = array();
					}

					$currentResultTree = &$currentResultTree[$pathArr[$i]];



					// Если в узле внутри нету ключа "element" - его нужно создать
					if (!isset($currentResultTree['element']))
					{
						//$currentResultTree['element'] = $this->getTreeNode($this->getTreeNodeCategory($pathArr , $i+1 ));
						$currentResultTree['element'] =
                            $this->$getTreeNodeFunction($this->getTreeNodeCategory($pathArr , $i+1 ));
					}
                    // Сбрасываем флаг empty у текущего узла
                    $currentResultTree['element']['empty'] = false;

				}
				// Нашли местечко для элемента - вставили в текущий узел
				// Проверим - есть ли в узле путь к узлу, который мы добавляем
				if (!isset($currentResultTree[$pathArr[count($pathArr)-1]]))
				{
					$currentResultTree[$pathArr[count($pathArr)-1]] = array();
				}
				$currentResultTree[$pathArr[count($pathArr)-1]]['element'] = 
					array();
				//$currentResultTree[$pathArr[count($pathArr)-1]]['element']=$this->getTreeNode($element);
				$currentResultTree[$pathArr[count($pathArr)-1]]['element']=$this->$getTreeNodeFunction($element);

			}
		}

	}
	
	public function sortTree(&$node = false) {
        
        if(!$node) {
        	$node = &$this->historyTree;	
        }
        // Вызываем сортировку по ключу для массива node
        ksort($node);
        // Перебираем детей элемента node и рекурсивно вызываем сортировку
        foreach ($node as $key => &$subNode)
        {
        	// Для элемента element не вызываем сортировку
			if ($key!="element")
			{
				$this->sortTree($subNode);
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
        if(!$this->previewMode) {
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
        } else {
            $actorsHistory = array();
        }
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

                if(!$this->previewMode && !$dependenceModel->save()) {
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