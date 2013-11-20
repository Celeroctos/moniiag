<?php
class TemplatesController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';
    private $pagesList = array( // Типы контролов
        'Приём больных'
    );

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

        $this->render('templatesView', array(
            'model' => new FormTemplateAdd(),
            'pagesList' => $this->pagesList,
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

            $model = new MedcardTemplate();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $templates = $model->getRows($filters, $sidx, $sord, $start, $rows);
            foreach($templates as $key => &$template) {
                $template['page'] = $this->pagesList[$template['page_id']];
                // Список категорий разбираем
                $template['categories'] = '';
                $categories = CJSON::decode($template['categorie_ids']);
                if(count($template['categorie_ids']) == 0) {
                    continue;
                }
                foreach($categories as $index => $catId) {
                    $categorie = MedcardCategorie::model()->find('id=:id', array(':id' => $catId));
                    $template['categories'] .= $categorie->name.', ';
                }
                if($template['categories'] != '') {
                    $template['categories'] = mb_substr($template['categories'], 0, mb_strlen($template['categories'], 'UTF-8') - 2, 'UTF-8');
                }
            }
            echo CJSON::encode(
                array('rows' => $templates,
                      'total' => $totalPages,
                      'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionEdit() {
        $model = new FormTemplateAdd();
        if(isset($_POST['FormTemplateAdd'])) {
            $model->attributes = $_POST['FormTemplateAdd'];
            if($model->validate()) {
                $template = MedcardTemplate::model()->find('id=:id', array(':id' => $_POST['FormTemplateAdd']['id']));
                $this->addEditModel($template, $model, 'Категория успешно добавлена.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionAdd() {
        $model = new FormTemplateAdd();
        if(isset($_POST['FormTemplateAdd'])) {
            $model->attributes = $_POST['FormTemplateAdd'];
            if($model->validate()) {
                $template = new MedcardTemplate();
                $this->addEditModel($template, $model, 'Элемент успешно добавлен.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }

    }

    private function addEditModel($template, $model, $msg) {
        $template->name = $model->name;
        $template->page_id = $model->pageId;
        if($model->categorieIds != null) {
            $template->categorie_ids = CJSON::encode($model->categorieIds);
        } else {
            $template->categorie_ids = CJSON::encode(array());
        }

        if($template->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
        }
    }

    public function actionDelete($id) {
        try {
            $template = MedcardTemplate::model()->findByPk($id);
            $template->delete();
            echo CJSON::encode(array('success' => 'true',
                'text' => 'Категория успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionGetone($id) {
        $model = new MedcardTemplate();
        $template = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $template)
        );
    }
}

?>