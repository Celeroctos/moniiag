<?php
class CategorieViewWidget extends CWidget {
    public $formModel = null;
    public $currentPatient = false;
    public $templateType = null;

    public function run() {
        $this->formModel = new FormTemplateDefault();
        $categories = $this->getCategories($this->templateType); // Шаблон страницы приёма
		/*echo "<pre>";
		var_dump($categories);
		exit();*/
        echo $this->render('application.modules.doctors.components.widgets.views.CategorieViewWidget', array(
            'categories' => $categories,
            'model' => $this->formModel,
            'currentPatient' => $this->currentPatient
        ));
    }

    // Получить иерархию категорий на странице
    protected function getCategories($pageId) {
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
				$categorieResult = $this->getCategorie($id);
                $categorieTemplateFill[] = $categorieResult;
            }
            $categoriesResult[] = $categorieTemplateFill;

        }
        return $categoriesResult;
    }
	
	public function getCategorie($id = false) {
		// Выбираем категорию
		$categorie = MedcardCategorie::model()->findByPk($id);
		$categorieResult = array();
		if($categorie != null) {
			$categorieResult['id'] = $categorie['id'];
			$categorieResult['name'] = $categorie['name'];
			$categorieResult['elements'] = array();

			$elementsModel = new MedcardElement();
			$elements = $elementsModel->getElementsByCategorie($categorie['id']);
			foreach($elements as $key => $element) {
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
						$element['label'] = $element['label'];
					}
				}
				// Добавляем в форму
				$this->formModel->setSafeRule('f'.$element['id']);
				$this->formModel->setAttributeLabels('f'.$element['id'], $element['label']);
				$fieldName = 'f'.$element['id'];
				$this->formModel->$fieldName = null;
				$element = $this->getFormValue($element);
				$categorieResult['elements'][] = $element;
			}
			// Теперь смотрим, есть ли дочерние элементы
			$categoriesChildren = MedcardCategorie::model()->findAll('parent_id = :parent_id', array(':parent_id' => $id));
			if(count($categoriesChildren) > 0) {
				// Дети есть. Для каждого из них вышеописанный процесс повторяется
				$categorieResult['children'] = array();
				foreach($categoriesChildren as $child) {
					$categorieResult['children'][] = $this->getCategorie($child->id);
				}
			}
		}
		return $categorieResult;
	}


    // Заполнить форму значениями
    public function getFormValue($element) {
        $medcardId = $this->currentPatient;
        if($this->formModel->medcardId == null) {
            $this->formModel->medcardId = $medcardId;
        }

        // Делаем выборку из базы значения
        $elementFinded = MedcardElementForPatient::model()->find('element_id = :element_id AND medcard_id = :medcard_id', array(':medcard_id' => $medcardId,
                                                                                                                                ':element_id' => $element['id'])
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
}
?>