<?php
class SheduleController extends Controller {
    public $layout = 'index';
    public $formModel = null;
    public $filterModel = null;
    public $currentPatient = false;

    public function actionView() {
        if(isset($_GET['cardid']) && trim($_GET['cardid']) != '') {
            // Проверим, есть ли такая медкарта вообще
            $medcardFinded = Medcard::model()->findByPk($_GET['cardid']);
            if($medcardFinded != null) {
                $this->currentPatient = trim($_GET['cardid']);
            }
        }

        $this->formModel = new FormTemplateDefault();
        $this->filterModel = new FormSheduleFilter();
        $categories = $this->getCategories(0); // Шаблон страницы приёма
        $patients = $this->getCurrentPatients();
       /*echo "<pre>";
        var_dump($patients);
        exit(); */
        $this->render('index', array(
            'categories' => $categories,
            'patients' => $patients,
            'currentPatient' => $this->currentPatient,
            'model' => $this->formModel,
            'filterModel' => $this->filterModel
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

                }
                $categorieTemplateFill[] = $categorieResult;
            }
            $categoriesResult[] = $categorieTemplateFill;

        }
        return $categoriesResult;
    }

    // Редактирование данных пациента
    public function actionPatientEdit() {
        if(isset($_POST['FormTemplateDefault'])) {
            // Перебираем весь входной массив, чтобы записать изменения в базу
            foreach($_POST['FormTemplateDefault'] as $field => $value) {
                if($field == 'medcardId') {
                    continue;
                }
                // Это для выпадающего списка с множественным выбором
                if(is_array($value)) {
                    $value = CJSON::encode($value);
                }
                // Проверим, есть ли такое поле вообще
                if(!preg_match('/^f(\d+)$/', $field, $resArr)) {
                    continue;
                }

                $element = MedcardElement::model()->findByPk($resArr[1]);
                if($element == null) {
                    continue;
                }
                // Дальше смотрим, есть ли уже такой элемент в базе для конкретного пациента. Если есть - будем апдейтить. Если нет - писать.
                $element = MedcardElementForPatient::model()->find('element_id = :element_id AND medcard_id = :medcard_id', array(':medcard_id' => $_POST['FormTemplateDefault']['medcardId'],
                                                                                                                                  ':element_id' => $element->id)
                                                                  );
                if($element == null) {
                    $elementModel = new MedcardElementForPatient();
                    $elementModel->medcard_id = $_POST['FormTemplateDefault']['medcardId'];
                    $elementModel->element_id = $resArr[1];
                    $elementModel->value = $value;
                    if(!$elementModel->save()) {
                        echo CJSON::encode(array('success' => true,
                                                 'text' => 'Ошибка сохранения новой записи.'));
                    }
                } else {
                    $element->value = $value;
                    if(!$element->save()) {
                        echo CJSON::encode(array('success' => true,
                                                 'text' => 'Ошибка сохранения записи.'));
                    }
                }
            }
        } else {
            echo CJSON::encode(array('success' => false,
                                     'text' => 'Ошибка запроса.'));
        }
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
        }

        return $element;
    }

    // Получить пациентов для текущего дня расписания
    public function getCurrentPatients() {
        if(!isset($_POST['FormSheduleFilter']['date'])) {
            $date = date('Y-m-d');
        } else {
            $this->filterModel->attributes = $_POST['FormSheduleFilter'];
            if($this->filterModel->validate()) {
                $date = $this->filterModel->date;
            } else {
                $date = date('Y-m-d');
            }
        }
        $this->filterModel->date = $date;
        $doctorId = Yii::app()->user->id;
        // Выбираем пациентов на обозначенный день
        $sheduleByDay = new SheduleByDay();
        $patients = $sheduleByDay->getRows($date, $doctorId);
        return $patients;
    }
}

?>