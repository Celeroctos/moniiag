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

            $categories = $model->getRows($filters, $sidx, $sord, $start, $rows);
			foreach($categories as &$categorie) {
				if($categorie['parent_id'] == null || $categorie['parent_id'] == -1) {
					$categorie['parent'] = 'Нет';
					if($categorie['parent_id'] == null) {
						$categorie['parent_id'] = -1;
					}
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

    public function actionEdit() {
        $model = new FormCategorieAdd();
        if(isset($_POST['FormCategorieAdd'])) {
            $model->attributes = $_POST['FormCategorieAdd'];
            if($model->validate()) {
                $categorie = MedcardCategorie::model()->find('id=:id', array(':id' => $_POST['FormCategorieAdd']['id']));
                $this->addEditModel($categorie, $model, 'Категория успешно добавлена.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
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
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }

    }

    private function addEditModel($categorie, $model, $msg) {
        $categorie->name = $model->name;
		$categorie->parent_id = $model->parentId;
        if($categorie->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
        }
    }

    public function actionDelete($id) {
        try {
            $categorie = MedcardCategorie::model()->findByPk($id);
            $categorie->delete();
            echo CJSON::encode(array('success' => 'true',
                'text' => 'Категория успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionGetone($id) {
        $model = new MedcardCategorie();
        $categorie = $model->getOne($id);
        if($categorie['parent_id'] == null) {
            $categorie['parent_id'] = -1;
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => $categorie)
        );
    }
}

?>