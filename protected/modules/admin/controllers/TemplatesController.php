<?php

class TemplatesController extends Controller {

    public $layout = 'application.modules.admin.views.layouts.index';

    private $pagesList = array( // Страницы
        'Основная медкарта',
        'Раздел рекомендаций'
    );

    public function getPagesList() {
        return $this->pagesList;
    }

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

            $order = array(
                'page' => 'page_id',
                'primary_diagnosis_desc' => 'primary_diagnosis',
                'categories' => 'categorie_ids'
            );
            if(isset($order[$sidx])) {
                $sidx = $order[$sidx];
            }

            $templates = $model->getRows($filters, $sidx, $sord, $start, $rows);
            foreach($templates as $key => &$template) {
                if($template['primary_diagnosis']) {
                    $template['primary_diagnosis_desc'] = 'Да';
                } else {
                    $template['primary_diagnosis_desc'] = 'Нет';
                }

                $template['page'] = $this->pagesList[$template['page_id']];
                // Список категорий разбираем
                $template['categories'] = '';
                $categories = CJSON::decode($template['categorie_ids']);
                if(count($template['categorie_ids']) == 0) {
                    continue;
                }

                foreach($categories as $index => $catId) {
                    $categorie = MedcardCategorie::model()->find('id=:id', array(':id' => $catId));
                    if($categorie != null) { // Фикс против нецелостности данных
                        $template['categories'] .= $categorie->name.', ';
                    }
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

        // Проверяем индекс
        $issetIndex = MedcardTemplate::model()->find('index = :index', array(
            'index' => $model->index
        ));

        if($issetIndex != null && $issetIndex->id != $template->id) {
            $indexes = MedcardTemplate::model()->getTemplateIndexes();
            $answer = array(
                'success' => false,
                'errors' => array(
                    'index' => array(
                        'Такой порядковый номер шаблона существует! (Занятые индексы: '
                    )
                )
            );

            foreach($indexes as $index) {
                if($index['index'] != null) {
                    $answer['errors']['index'][0] .= $index['index'].', ';
                }
            }
            $answer['errors']['index'][0] = mb_substr($answer['errors']['index'][0], 0, mb_strlen($answer['errors']['index'][0]) - 2).')';
            echo CJSON::encode($answer);
            exit();
        }

        $template->index = $model->index;
        $template->primary_diagnosis = $model->primaryDiagnosisFilled;

        if($model->categorieIds != null) {
            $template->categorie_ids = CJSON::encode($model->categorieIds);
        } else {
            $template->categorie_ids = CJSON::encode(array());
        }

        if($template->save()) {
            echo CJSON::encode(array(
                'success' => true,
                'text' => $msg,
                'model' => $model
            ));
        }
    }
	
	public function actionIssetMedworkerPerTempl($id) {
		$checked = EnabledTemplate::model()->getByTemplateId($id);
		echo CJSON::encode(array(
			'success' => true,
			'issetChecked' => count($checked) > 0,
			'medworkers' => $checked
		));
	}

    public function actionDelete($id) {
        $errorTextMessage = 'На данную запись есть ссылки!';

        try {
            $template = MedcardTemplate::model()->findByPk($id);
            $template->delete();
            echo CJSON::encode(array('success' => 'true',
                'text' => 'Категория успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                'error' => $errorTextMessage ));
        }
    }

    public function actionGetone($id) {
        $model = new MedcardTemplate();
        $template = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                'data' => $template)
        );
    }

    // Просмотр шаблона
    public function actionShow() {

        $categorieWidget = CWidget::createWidget('application.modules.doctors.components.widgets.CategorieViewWidget', array(
            'currentPatient' => null,
            'templateType' => 0,
            'templateId' => $_GET['id'],
            'withoutSave' => 0,
            'greetingId' => null,
            'canEditMedcard' => 1,
            'medcard' => null,
            'currentDate' => null,
            'templatePrefix' => 'a'.$_GET['id'],
            'previewMode' => true
        ));

        $templateView = $categorieWidget->run();
        ob_end_clean();
        
        echo CJSON::encode(array(
                'success' => true,
                'data' => $templateView
            )
        );
    }

	public function actionUtc() {

		$tid = $_POST["tid"];
		$categories = $_POST["categories"];
		$cids = $_POST["cids"];

		// update template array with categories
		MedcardTemplate::model()->setTemplateCategories($tid, $cids);

		// decode new categories parents and positions
		$categoriesArray = json_decode($categories);

		foreach ($categoriesArray as $i => $child) {
            if ($child->category != -1) {
                $path = MedcardCategorie::model()->findByPk((int)$child->category)->path.".".$child->position;
            } else {
                $path = $child->position;
            }
			if ($child->type == "element") {
				MedcardElement::model()->updateByPk($child->id, array(
					"position" => $child->position,
					"categorie_id" => $child->category,
                    "path" => $path
				));
			} else {
				MedcardCategorie::model()->updateByPk($child->id, array(
					"position" => $child->position,
					"parent_id" => $child->category,
                    "path" => $path
				));
			}
		}

		echo json_encode(array(
			'status' => true
		));
	}

	private function assignChildren(&$row, $model) {

		$id = intval($row["id"]);

		// fetch category children
		$children = $model->getChildren($id);

		// assign children to every child
		foreach ($children as $i => &$child) {
			$this->assignChildren($child, $model);
		}

		// assign children array
		$row["children"] = $children;

		// fetch all category elements and assign to category
		$row["elements"] = $model->getElements($id);
	}

    public function actionGetCategories($id) {

        // cast category identifier to int (just in case)
        $id = intval($id);

        $templateModel = new MedcardTemplate();
        $categoryModel = new MedcardCategorie();

        // fetch template by it's identifier
        $template = $templateModel->getOne($id);

        // decode template's categories array
        $categories = json_decode($template['categorie_ids']);

        // we wil store here all fetched categories
        $templateCategories = array();

        foreach ($categories as $i => $id) {

            // fetch category from db
            $category = $categoryModel->getOne(intval($id));

			$this->assignChildren($category, $categoryModel);

            // push category to array
            $templateCategories[] = $category;
        }

        // save all found categories as template field
        $template["categories"] = $templateCategories;

        echo CJSON::encode(array('success' => true,
                'template' => $template
            )
        );
    }
}