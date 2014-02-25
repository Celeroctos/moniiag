<?php
class ElementsController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';

    public function actionView() {
        // Категории
        $categoriesModel = new MedcardCategorie();
        $categories = $categoriesModel->getRows(false);
        $categoriesList = array();
        foreach($categories as $index => $categorie) {
            $categoriesList[$categorie['id']] = $categorie['name'];
        }

        // Справочники
        $guidesModel = new MedcardGuide();
        $guides = $guidesModel->getRows(false);
        $guidesList = array('-1' => 'Нет');
        foreach($guides as $index => $guide) {
            $guidesList[$guide['id']] = $guide['name'];
        }

        $elementModel = new MedcardElement();
        $this->render('elementsView', array(
            'model' => new FormElementAdd(),
            'typesList' => $elementModel->getTypesList(),
            'categoriesList' => $categoriesList,
            'guidesList' => $guidesList
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

            $elements = $model->getRows($filters, $sidx, $sord, $start, $rows);
            $typesList = $model->getTypesList();
            foreach($elements as $key => &$element) {
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
            if($issetPositionInElements->id != $element->id) {
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

        if($model->guideId != -1) { // Если справочник выбран
            $element->guide_id = $model->guideId;
        }
        if($model->type == 2 || $model->type == 3) {
            $element->allow_add = $model->allowAdd;
        } else {
            $element->allow_add = 0;
        }

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
        try {
            $element = MedcardElement::model()->findByPk($id);
            $element->delete();
            echo CJSON::encode(array('success' => 'true',
                'text' => 'Категория успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionGetone($id) {
        $model = new MedcardElement();
        $element = $model->getOne($id);
        if($element['guide_id'] == null) {
            $element['guide_id'] = -1;
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => $element)
        );
    }
}

?>