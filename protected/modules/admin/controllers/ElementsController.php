<?php
class ElementsController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';

    public function actionView() {
        // Категории
        $categoriesModel = new MedcardCategorie();
        $categories = $categoriesModel->getRows(false, 'name', 'asc');
        $categoriesList = array();
        foreach($categories as $index => $categorie) {
            $categoriesList[$categorie['id']] = $categorie['name'];
        }

        // Справочники
        $guidesModel = new MedcardGuide();
        $guides = $guidesModel->getRows(false, 'name', 'asc');
        $guidesList = array('-1' => 'Нет');
        foreach($guides as $index => $guide) {
            $guidesList[$guide['id']] = $guide['name'];
        }

        $elementModel = new MedcardElement();
        $this->render('elementsView', array(
            'model' => new FormElementAdd(),
            'typesList' => $elementModel->getTypesList(),
            'categoriesList' => $categoriesList,
            'guidesList' => $guidesList,
            'guideValuesList' => array('-1' => 'Нет')
        ));
    }

    public function actionGet() {
        try {
            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];

            // Фильтры поиска
            if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
                $filters = CJSON::decode($_GET['filters']);
            } else {
                $filters = false;
            }

            $model = new MedcardElement();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $order = array(
                'is_wrapped_name' => 'is_wrapped'
            );
            if(isset($order[$sidx])) {
                $sidx = $order[$sidx];
            }

            $elements = $model->getRows($filters, $sidx, $sord, $start, $rows);
            $typesList = $model->getTypesList();
            foreach($elements as $key => &$element) {
                if($element['config'] != null) {
					$element['config'] = CJSON::decode($element['config']);
					if(isset($element['config']['showDynamic'])) {
						$element['show_dynamic_desc'] = 'Да'; 
						$element['show_dynamic'] = 1;
					}
				} else {
					$element['show_dynamic_desc'] = 'Нет'; 
					$element['show_dynamic'] = 0;
				}
				$temp = $element['type'];
                $element['type_id'] = $temp;
                $element['type'] = $typesList[$element['type']];
                if($element['guide_id'] == null) {
                    $element['guide_id'] = -1;
                }
                if($element['is_wrapped'] != 1) {
                    $element['is_wrapped_name'] = 'Нет';
                } else {
                    $element['is_wrapped_name'] = 'Да';
                }
            }
            echo CJSON::encode(
                array('rows' => $elements,
                    'total' => $totalPages,
                    'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

   public function actionEdit() {
        $model = new FormElementAdd();
        if(isset($_POST['FormElementAdd'])) {
            $model->attributes = $_POST['FormElementAdd'];
            // Проверим - изменился ли тип у элемента,

            if ($_POST['FormElementAdd']['id']!='')
            {


                // Найдём по id элемент
                $oldElementState = MedcardElement::model()->findByPk($_POST['FormElementAdd']['id']);
                // Вытащим зависимости
                // Проверить - есть ли зависимости на элементе (причём и в ту и в другую сторону)

                // Если у старого элемента тип 2 или 3 - проверяем зависимости

                if (($oldElementState['type']==2 )||($oldElementState['type']==3 ))
                {
                    $existanceDependencies = MedcardElementDependence::model()->findAll(
                        'element_id = :ahead_element OR dep_element_id = :back_element',
                        array( ':ahead_element'=>$_POST['FormElementAdd']['id'], ':back_element' => $_POST['FormElementAdd']['id'] )
                    );
                   // var_dump($existanceDependencies);
                   // exit();
                    // Если счёт зависимостей больше нуля и тип изменился - выводим сообщение об ошибке
                    if ((count($existanceDependencies)>0) && ($_POST['FormElementAdd']['type']!=$oldElementState['type']))
                    {
                        echo CJSON::encode(array('success' => 'false',
                            'errors' => array(array( 'Не удалось изменить элемент, так как при редактировании был изменён тип элемента. Если на элементе заданы зависимости, то нельзя менять его тип.')) ));
                        exit();
                    }
                    // Если счёт зависимостей больше нуля и изменился ИД справочника - также выводим сообщение об ошибке
                    if ((count($existanceDependencies)>0) && ($_POST['FormElementAdd']['guideId']!=$oldElementState['guide_id']))
                    {
                        echo CJSON::encode(array('success' => 'false',
                            'errors' => array(array( 'Не удалось изменить элемент, так как при редактировании был изменён справочник элемента. Если на элементе заданы зависимости, то нельзя менять его справочник.')) ));
                        exit();
                    }
                }
            }

            if($model->validate()) {
                $element = MedcardElement::model()->find('id=:id', array(':id' => $_POST['FormElementAdd']['id']));
                $this->addEditModel($element, $model, 'Категория успешно добавлена.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionAdd() {
		
        $model = new FormElementAdd();
        if(isset($_POST['FormElementAdd'])) {
            $model->attributes = $_POST['FormElementAdd'];
            if($model->validate()) {
                $element = new MedcardElement();
                $this->addEditModel($element, $model, 'Элемент успешно добавлен.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }

    }

    private function addEditModel($element, $model, $msg) {
        // Посмотрим, нет ли уже элемента с такой позицией в данной категории: категории или элемента:
        $issetPositionInCats = MedcardCategorie::model()->find('position = :position AND parent_id = :categorie_id', array(':position' => $model->position, ':categorie_id' => $model->categorieId));
        $issetPositionInElements = MedcardElement::model()->find('position = :position AND categorie_id = :categorie_id', array(':position' => $model->position, ':categorie_id' => $model->categorieId));
        if($issetPositionInCats != null || $issetPositionInElements != null) {
            if(($issetPositionInElements != null && $issetPositionInElements->id != $element->id) || $issetPositionInCats != null) {
                echo CJSON::encode(array('success' => false,
                        'errors' => array(
                            'position' => array(
                                'Такая позиция в данной категории существует (среди категорий или элементов).'
                            )
                        )
                    )
                );
                exit();
            }
        }

        $element->type = $model->type;
        $element->categorie_id = $model->categorieId;
        $element->label = $model->label;
        $element->label_after = $model->labelAfter;
        $element->size = $model->size;
        $element->is_wrapped = $model->isWrapped;
        $element->position = $model->position;
        $element->label_display = $model->labelDisplay;
        $element->is_required = $model->isRequired;
        
        if($model->guideId != -1) { // Если справочник выбран
            $element->guide_id = $model->guideId;
        }
        if($model->type == 2 || $model->type == 3 || $model->type == 7) {
            $element->allow_add = $model->allowAdd;
        }
        if($model->type == 2 || $model->type == 3) {
            //$element->allow_add = $model->allowAdd;
            if($model->defaultValue != -1) {
                $element->default_value = $model->defaultValue;
            } else {
                $element->default_value = null;
            }
        } else {
          //  $element->allow_add = 0;
            // Если текст или текстовая область - берём другое поле модели
            if ($model->type == 0 || $model->type == 1)
            {
                $element->default_value = $model->defaultValueText;
            }
            else
            {
                $element->default_value = null;
            }
        }
		
		$config = array();
        if($model->type == 4) {
            $config += $model->config;
        }
		if($model->type == 5) {
			if($model->numberFieldMaxValue != null && $model->numberFieldMinValue != null && $model->numberFieldMaxValue < $model->numberFieldMinValue) {
                echo CJSON::encode(array('success' => false,
                        'errors' => array(
                            'maxminvalue' => array(
                                'Максимальное значение поля меньше, чем минимальное!'
                            )
                        )
                    )
                );
                exit();
            }

			
                $config['maxValue'] = $model->numberFieldMaxValue;
                $config['minValue'] = $model->numberFieldMinValue;
                $config['step'] = $model->numberStep;
            
		}

		if($model->showDynamic) {
			$config['showDynamic'] = 1;
		}
		
		if ($model->type == 6) {
            // Проверим - больше ли максимальное значение минимального
            if (strtotime($model->dateFieldMaxValue)<strtotime($model->dateFieldMinValue))
            {
                echo CJSON::encode(array('success' => false,
                        'errors' => array(
                            'maxminvalue' => array(
                                'Максимальное значение поля меньше, чем минимальное!'
                            )
                        )
                    )
                );
                exit();
            }
			
				$config['maxValue'] = $model->dateFieldMaxValue;
				$config['minValue'] = $model->dateFieldMinValue;
		}

		$element->config = CJSON::encode($config); 

        // Теперь посчитаем путь до элемента. Посмотрим на категорию, выберем иерархию категорий и прибавим введённую позицию
        $partOfPath = $this->getElementPath($element->categorie_id);
        $partOfPath = implode('.', array_reverse(explode('.', $partOfPath)));
        $element->path = $partOfPath.'.'.$element->position;

        if($element->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
        }
    }

    // Путь элемента считается в категории
    private function getElementPath($categorieId) {
        $categorie = MedcardCategorie::model()->findByPk($categorieId);
        $path = '';
        if($categorie != null) {
            // Для построения пути не пойдёт: в иерархии должно быть всё определено
            if($categorie->position == null) {
                echo CJSON::encode(array('success' => false,
                        'errors' => array(
                            'position' => array(
                                'Одна или несколько позиций в иерархии имеет неприсвоенные позиции!'
                            )
                        )
                    )
                );
                exit();
            }

            if($categorie->parent_id != -1) {
                $path .= '.'.$this->getElementPath($categorie->parent_id);
                return $categorie->position.$path;
            } else {
                return $categorie->position;
            }
        } else {
            echo CJSON::encode(array('success' => false,
                    'errors' => array(
                        'position' => array(
                            'Одна или несколько позиций в иерархии отсутствует!'
                        )
                    )
                )
            );
            exit();
        }
        return $path;
    }

    public function actionDelete($id) {

        $errorMessageText = 'На данную запись есть ссылки!';
        try {
            $element = MedcardElement::model()->findByPk($id);
            // Проверить - есть ли зависимости на элементе (причём и в ту и в другую сторону)
            $oneWayDependencies = MedcardElementDependence::model()->findAll(
                'element_id = :ahead_element OR dep_element_id = :back_element',
                array( ':ahead_element'=>$id, ':back_element' => $id )
            );

            if (count ($oneWayDependencies) > 0 )
            {
                $errorMessageText = 'Нельзя удалить элемент - на него поставлены зависимости.';
                throw new Exception($errorMessageText);
            }

            $element->delete();
            echo CJSON::encode(array('success' => 'true',
                'text' => 'Категория успешно удалена.'));
        } catch(Exception $e) {
            // Ааааашипка
            echo CJSON::encode(array('success' => 'false',
                'error' => $errorMessageText));
        }
    }

    public function actionGetone($id) {
        $model = new MedcardElement();
        $element = $model->getOne($id);
        if($element['guide_id'] == null) {
            $element['guide_id'] = -1;
        }
        if(($element['default_value'] == null) && ($element['type']!=0)&&($element['type']!=1)) {
            $element['default_value'] = -1;
        }
		if($element['config'] != null) {
			$element['config'] = CJSON::decode($element['config']);
			if(isset($element['config']['showDynamic'])) {
				$element['show_dynamic'] = 1;
			} else {
				$element['show_dynamic'] = 0;
			}
		} else {
			$element['show_dynamic'] = 0;
		}

        // Нужно выяснить - есть ли на элементе зависимости (если есть - нужно зыблокировать некоторые варианты в селекте типа)
        $existanceDependencies = MedcardElementDependence::model()->findAll(
            'element_id = :ahead_element',
            array( ':ahead_element'=> $id )
        );

        if (count($existanceDependencies)>0)
        {
            $element['is_dependencies'] = 1;
        }
        else
        {
            $element['is_dependencies'] = 0;
        }

        echo CJSON::encode(array('success' => true,
                                 'data' => $element)
        );
    }

    // Получение зависимостей элементов
    public function actionGetDependences($id) {
        $dependencesArr = $this->getDependences($id);
        // Получаем категорию контрола
        $elementModel = MedcardElement::model()->findByPk($id);
        if($elementModel == null) {
            exit('Элемент №'.$id.' не имеет категории!');
        }

        // Получаем все контролы
        $controls = MedcardElement::model()->findAll('id != :element_id AND categorie_id = :categorie_id ORDER BY label', array(':element_id' => $id, ':categorie_id' => $elementModel->categorie_id));
        foreach($controls as &$control) {
            if(trim($control['label_display']) != '' && $control['label_display'] != null) {
                $control['label'] .= ' ('.$control['label_display'].')';
            }
        }
        $comboValues = MedcardElement::model()->getGuideValuesByElementId($id);
        echo CJSON::encode(array(
             'success' => true,
             'data' => array(
                 'dependences' => $dependencesArr,
                 'comboValues' => $comboValues,
                 'controls' => $controls,
                 'actions' => array(
                     'Нет',
                     'Скрыть',
                     'Показать'
                 )
               )
            )
        );
    }

    public function actionGetDependencesList() {
        if(isset($_GET['id'])) {
            $elementModel = MedcardElement::model()->findByPk($_GET['id']);
            if($elementModel != null) {
                $categorieId = $elementModel->categorie_id;
            } else {
                $categorieId = false;
            }
        } else {
            $categorieId = false;
        }
        echo CJSON::encode(array(
                'success' => true,
                'rows' => $this->getDependences(false, $categorieId)
            )
        );
    }

    private function getDependences($id, $categorieId = false) {
        //$dependences = MedcardElementDependence::model()->findAll('element_id = :element_id', array(':element_id' => $id));
        $dependences = MedcardElementDependence::model()->getRows($id, $categorieId);
        $dependencesArr = array();
        foreach($dependences as $dependence) {
            $dependence['actionId'] = $dependence['action'];
            $dependence['action'] = $dependence['action'] == 1 ? 'Скрыть' : 'Показать';
            if($dependence['me_display_label'] != null) {
                $dependence['element'] .= ' ('.$dependence['me_display_label'].')';
            }
            if($dependence['me2_display_label'] != null) {
                $dependence['dep_element'] .= ' ('.$dependence['me2_display_label'].')';
            }
            $dependencesArr[] = $dependence;
        }
        return $dependencesArr;
    }

    // Сохранить все зависимости. Есть зависимость == "Нет", это означает, что строку из базы зависимостей надо удалить
    public function actionSaveDependences($values, $dependenced, $action, $controlId) {
        $values = CJSON::decode($values);
        $dependenced = CJSON::decode($dependenced);

        foreach($values as $value) {
            foreach($dependenced as $dependence) {
                // Это удаление зависимостей
                if($action == 0) {
                    MedcardElementDependence::model()->deleteAll('
                        element_id = :element_id
                        AND value_id = :value_id
                        AND dep_element_id = :dep_element_id
                    ', array(
                        ':element_id' => $controlId,
                        ':value_id' => $value,
                        ':dep_element_id' => $dependence
                    ));
                } else { // В противном случае, это установка зависимостей
                    // Может быть только один элемент у зависимого контрола, от которого ставится зависимость
                    $issetDependence =  MedcardElementDependence::model()->findAll('
                        dep_element_id = :dep_element_id
                        AND value_id = :value_id
                    ', array(
                        ':dep_element_id' => $dependence,
                        //--------
                        ':value_id' => $value
                        //--------
                    ));
                    if(count($issetDependence) == 0) {
                        $dependenceElement = new MedcardElementDependence();
                        $dependenceElement->element_id = $controlId;
                        $dependenceElement->value_id = $value;
                        $dependenceElement->dep_element_id = $dependence;
                        $dependenceElement->action = $action;
                        if(!$dependenceElement->save()) {
                            exit('Не могу сохранить зависимость!');
                        }
                    } else {
                        foreach($issetDependence as $dep) {
                            $depModel = MedcardElementDependence::model()->find('
                                element_id = :element_id
                                AND value_id = :value_id
                                AND dep_element_id = :dep_element_id
                            ', array(
                                ':element_id' => $controlId,
                                ':value_id' => $value,
                                ':dep_element_id' => $dependence
                            ));

                            $depModel->action = $action;
                            if(!$depModel->save()) {
                                exit('Не могу сохранить зависимость!');
                            }
                        }
                    }
                }
            }
        }

        echo CJSON::encode(array(
                'success' => true,
                'data' => array()
            )
        );
    }
}

?>