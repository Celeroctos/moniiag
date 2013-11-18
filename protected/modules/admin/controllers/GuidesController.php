<?php
class GuidesController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';

    public function actionView() {
        $this->render('guidesView', array(
            'model' => new FormGuideAdd()
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

            $model = new MedcardGuide();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $guides = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(
                array('rows' => $guides,
                    'total' => $totalPages,
                    'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionEdit() {
        $model = new FormGuideAdd();
        if(isset($_POST['FormGuideAdd'])) {
            $model->attributes = $_POST['FormGuideAdd'];
            if($model->validate()) {
                $guide = MedcardGuide::model()->find('id=:id', array(':id' => $_POST['FormGuideAdd']['id']));
                $this->addEditModel($guide, $model, 'Категория успешно добавлена.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionAdd() {
        $model = new FormGuideAdd();
        if(isset($_POST['FormGuideAdd'])) {
            $model->attributes = $_POST['FormGuideAdd'];
            if($model->validate()) {
                $guide = new MedcardGuide();
                $this->addEditModel($guide, $model, 'Категория успешно добавлена.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }

    }

    private function addEditModel($guide, $model, $msg) {
        $guide->name = $model->name;
        if($guide->save()) {
            echo CJSON::encode(array('success' => true,
                'text' => $msg));
        }
    }

    public function actionDelete($id) {
        try {
            $guide = MedcardGuide::model()->findByPk($id);
            $guide->delete();
            echo CJSON::encode(array('success' => 'true',
                'text' => 'Категория успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionGetone($id) {
        $model = new MedcardGuide();
        $guide = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                'data' => $guide)
        );
    }
}

?>