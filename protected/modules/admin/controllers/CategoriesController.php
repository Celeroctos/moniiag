<?php
class CategoriesController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';

    public function actionView() {
		$categoriesList = array('-1' => 'Нет');
		// Получить все категории 
		$categoriesModel = new MedcardCategorie();
        $categories = $categoriesModel->getRows(false,  'name', 'asc', false, false);

        foreach($categories as $index => $categorie) {
            $categoriesList[$categorie['id']] = $categorie['name'];
        }

        $this->render('catView', array(
            'model' => new FormCategorieAdd(),
			'categoriesList' => $categoriesList
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

            $model = new MedcardCategorie();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $order = array(
                'is_dynamic_name' => 'is_dynamic',
                'wrapped' => 'is_wrapped'
            );
            if(isset($order[$sidx])) {
                $sidx = $order[$sidx];
            }

            $categories = $model->getRows($filters, $sidx, $sord, $start, $rows);
			foreach($categories as &$categorie) {
				if($categorie['parent_id'] == null || $categorie['parent_id'] == -1) {
					$categorie['parent'] = 'Нет';
					if($categorie['parent_id'] == null) {
						$categorie['parent_id'] = -1;
					}
				}
                if($categorie['is_dynamic'] == 1) {
                    $categorie['is_dynamic_name'] = 'Да';
                } else {
                    $categorie['is_dynamic_name'] = 'Нет';
                }
                if($categorie['is_wrapped'] == 1) {
                    $categorie['wrapped'] = 'Да';
                } else {
                    $categorie['wrapped'] = 'Нет';
                }
			}
            echo CJSON::encode(
                array('rows' => $categories,
                    'total' => $totalPages,
                    'records' => count($num),
                    'success' => 'true')
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

	private function assignChildren(&$row, $categoryModel = null) {
		if ($categoryModel == null) {
			$categoryModel = new MedcardCategorie();
		}
		$id = intval($row["id"]);
		$children = $categoryModel->getChildren($id);
		foreach ($children as $i => &$child) {
			$this->assignChildren($child, $categoryModel);
		}
		$row["children"] = $children;
		$row["elements"] = $categoryModel->getElements($id);
	}

	public function actionOne($id) {
		$categoryModel = new MedcardCategorie();
		$category = $categoryModel->getOne($id);
		$this->assignChildren($category, $categoryModel);
		echo json_encode(array(
			'model' => $category
		));
	}

	private function cloneCategoriesAndElements(&$category) {
		$row = new MedcardCategorie();
		$row->name = $category["name"];
		$row->parent_id = $category["parent_id"];
		$row->position = $category["position"];
		$row->is_dynamic = $category["is_dynamic"];
		$row->path = $category["path"];
		$row->is_wrapped = $category["is_wrapped"];
		$row->save();
		$categoryRowID = $row->id;
		$category["id"] = $categoryRowID;
		foreach ($category["elements"] as $i => &$element) {
			$row = new MedcardElement();
			$row->type = $element["type"];
			$row->categorie_id = $categoryRowID;
			$row->label = $element["label"];
			$row->guide_id = $element["guide_id"];
			$row->allow_add = $element["allow_add"];
			$row->label_after = $element["label_after"];
			$row->size = $element["size"];
			$row->is_wrapped = $element["is_wrapped"];
			$row->path = $element["path"];
			$row->position = $element["position"];
			$row->config = $element["config"];
			$row->default_value = $element["default_value"];
			$row->label_display = $element["label_display"];
			$row->is_required = $element["is_required"];
			$row->not_printing_values = $element["not_printing_values"];
			$row->hide_label_before = $element["hide_label_before"];
			$row->save();
			$element["categorie_id"] = $categoryRowID;
			$element["id"] = $row->id;
		}
		foreach ($category["children"] as $i => &$category) {
			$category["parent_id"] = $categoryRowID;
			$this->cloneCategoriesAndElements($category);
		}
	}

	public function actionClone($id) {
		$categoryModel = new MedcardCategorie();
		$elementModel = new MedcardElement();
		$category = $categoryModel->getOne($id);
		$this->assignChildren($category, $categoryModel);
		$this->cloneCategoriesAndElements($category, $categoryModel, $elementModel);
		echo json_encode(array(
			'model' => $category
		));
	}

	public function actionMove($id) {
		/* $category = MedcardCategorie::model()->findByPk($id);
		if ($category["parent_id"] == -1) {
			echo json_encode(array(
				"success" => false,
				"error" => "Данная категория уже имеет своего родителя"
			)); die;
		} */
		MedcardCategorie::model()->updateByPk($id, array(
			"parent_id" => -1
		));
		$category = MedcardCategorie::model()->getOne($id);
		$this->assignChildren($category);
		echo json_encode(array(
			"success" => true,
			"model" => $category
		)); die;
	}

    public function actionEdit() {
        $model = new FormCategorieAdd();
        if(isset($_POST['FormCategorieAdd'])) {
            $model->attributes = $_POST['FormCategorieAdd'];
            if($model->validate()) {
                $categorie = MedcardCategorie::model()->find('id=:id', array(
					':id' => $_POST['FormCategorieAdd']['id']
				));
                $this->addEditModel($categorie, $model, 'Категория успешно добавлена.');
            } else {
                echo CJSON::encode(array(
					'success' => 'false',
					'errors' => $model->errors
				));
            }
        }
    }

    public function actionAdd() {
        $model = new FormCategorieAdd();
        if(isset($_POST['FormCategorieAdd'])) {
            $model->attributes = $_POST['FormCategorieAdd'];
            if($model->validate()) {
                $categorie = new MedcardCategorie();
                $this->addEditModel($categorie, $model, 'Категория успешно добавлена.');
            } else {
                echo CJSON::encode(array(
					'success' => 'false',
                    'errors' => $model->errors
				));
            }
        }
    }

    private function addEditModel($categorie, $model, $msg) {

		/* $issetPositionInCats = MedcardCategorie::model()->find('position = :position AND parent_id = :parent_id', array(
			':position' => $model->position,
			':parent_id' => $model->parentId
		));
        $issetPositionInElements = MedcardElement::model()->find('position = :position AND categorie_id = :categorie_id', array(
			':position' => $model->position,
			':categorie_id' => $model->parentId
		));
        if($issetPositionInCats != null || $issetPositionInElements != null) {
            if(($issetPositionInCats != null && $issetPositionInCats->id != $categorie->id) || $issetPositionInElements != null) {
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
        } */

        $categorie->name = $model->name;

		if ($model->parentId) {
			$categorie->parent_id = $model->parentId;
		}

        $categorie->is_dynamic = $model->isDynamic;
        $savedPosition = $categorie->position;

		if ($model->position) {
			$categorie->position = $model->position;
		}

        if($categorie->parent_id != -1) {
            $partOfPath = $this->getCategoriePath(MedcardCategorie::model()->findByPk($categorie->parent_id));
            $partOfPath = implode('.', array_reverse(explode('.', $partOfPath)));
            $categorie->path = $partOfPath.'.'.$categorie->position;
        } else {
            $categorie->path = $categorie->position; // Корневой элемент в графе
        }

        $categorie->is_wrapped = $model->isWrapped;
        $isOk = $categorie->save();

        // Теперь считаем путь элемента. В том случае, если позиции до изменения категории не
		// совпадают с позициями после изменения, нужно пересчитать все зависимые элементы,
		// которыми могут быть категории и элементы
        // Если это новая категория, то она не вторгается в иерархию, менять пути не надо
        if($categorie->id != null && $savedPosition != $model->position) {
            $this->changePaths($categorie->id);
        }

        if($isOk) {
            echo CJSON::encode(array(
                'success' => true,
                'text' => $msg,
                'model' => $categorie
            ));
        }
    }

    // Сменить пути у категорий и элементов: выборка категорий и элементов
    private function changePaths($categorieId) {
        $categories = MedcardCategorie::model()->findAll('parent_id = :parent_id', array(':parent_id' => $categorieId));
        $elements = MedcardElement::model()->findAll('categorie_id = :categorie_id', array(':categorie_id' => $categorieId));
        foreach($categories as $categorie) {
            $partOfPath = $this->getCategoriePath($categorie);
            $partOfPath = implode('.', array_reverse(explode('.', $partOfPath)));
            $path = $partOfPath;

            $categorieModel = MedcardCategorie::model()->findByPk($categorie->id);
            $categorieModel->path = $path;
            if(!$categorieModel->save()) {
                exit('Не удалось сохранить категорию медкарты :(');
            }
            // Эта категория может быть родительской для каких-нибудь ещё категорий, а также содержать в себе элементы
            $this->changePaths($categorieModel->id);
        }
        foreach($elements as $element) {
            $partOfPath = $this->getElementPath($element->categorie_id);
            $partOfPath = implode('.', array_reverse(explode('.', $partOfPath)));

            $categorieModel = MedcardCategorie::model()->findByPk($element->categorie_id);

            $elementModel = MedcardElement::model()->findByPk($element->id);
            $elementModel->path = $partOfPath.'.'.$element->position;
            if(!$elementModel->save()) {
                exit('Не удалось сохранить элемент медкарты :(');
            }
            // А вот элемент родительским быть не может
        }
    }

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

    private function getCategoriePath($categorie) {
        $path = '';
        if($categorie->parent_id == -1) {
            return $categorie->position;
        } else {
            $path .= '.'.$this->getElementPath($categorie->parent_id);
            return $categorie->position.$path;
        }
    }

    public function actionDelete($id) {
        try {
			// Replaced with Trigger
			/*MedcardElement::model()->deleteAll("categorie_id = :id", array(
				":id" => $id
			)); */
			/*MedcardCategorie::model()->updateAll(array(
				"parent_id" => $id
			), array(
				"parent_id" => -1
			));
			MedcardElement::model()->updateAll(array(
				"categorie_id" => $id
			), array(
				"categorie_id" => -1
			));*/
			MedcardCategorie::model()->deleteByPk($id);
            echo CJSON::encode(array(
				'success' => true,
                'text' => 'Категория успешно удалена.'
			));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array(
				'success' => false,
				'message' => $e->getMessage(),
                'error' => 'На данную запись есть ссылки!'
			));
        }
    }

    public function actionGetone($id) {
        $model = new MedcardCategorie();
        $categorie = $model->getOne($id);
        if($categorie['parent_id'] == null) {
            $categorie['parent_id'] = -1;
        }
        if($categorie['is_dynamic'] == null) {
            $categorie['is_dynamic'] = 0;
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => $categorie)
        );
    }
	
	public function actionGetMatches($pattern) {
		$model = new MedcardCategorie();
        $categories = $model->getRows($id);
		foreach ($categories as $i => $categorie) {
			if($categorie['parent_id'] == null) {
				$categorie['parent_id'] = -1;
			}
			if($categorie['is_dynamic'] == null) {
				$categorie['is_dynamic'] = 0;
			}
		}
        echo CJSON::encode(array('success' => true,
                                 'data' => $categories)
        );
	}

    public function actionClearGreetingsData() {
        MedcardElementPatientDependence::model()->deleteAll();
        MedcardElementForPatient::model()->deleteAll();
		PatientDiagnosis::model()->deleteAll();
        ClinicalPatientDiagnosis::model()->deleteAll();
        MedcardRecord::model()->deleteAll();
        echo CJSON::encode(array('success' => true,
                                 'data' => 'Таблицы данных приёмов успешно очищены.')
        );
    }
}

?>