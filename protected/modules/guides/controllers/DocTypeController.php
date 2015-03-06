<?php
class DocTypeController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';
    public function actionView() {
        $formAddEdit = new FormDocTypeAdd();
        //var_dump('!');
        //exit();
        $this->render('view', array(
            'model' => $formAddEdit
        ));
    }

    public function actionGetOne($id) {
        $model = new Doctype();
        $doctype = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                'data' => $doctype)
        );
    }

    public function actionGet() {
        try {
            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];

            if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
                $filters = CJSON::decode($_GET['filters']);
            } else {
                $filters = false;
            }

            $model = new Doctype();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $doctypes = $model->getRows($filters, $sidx, $sord, $start, $rows);
            echo CJSON::encode(
                array(
                    'success' => true,
                    'rows' => $doctypes,
                    'total' => $totalPages,
                    'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

/*
    public function actionDelete($id) {
        try {
            $insurance = Insurance::model()->findByPk($id);
            $insurance->delete();
            echo CJSON::encode(array('success' => 'true',
                'text' => 'Страховая компания успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }
*/

    public function actionDelete($id) {
        try {
            $doctype = Doctype::model()->findByPk($id);
            $doctype->delete();
            echo CJSON::encode(array('success' => 'true',
                'text' => 'Тип удостоверения личности успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }

    private function addEditModel($doctype, $model, $msg) {
        $doctype->name = $model->name;
        //var_dump($doctype);
        //exit();
        if($doctype->save()) {
            echo CJSON::encode(array(
                    'success' => true,
                    'text' =>  $msg
                )
            );
        }
    }

    public function actionEdit() {
        $model = new FormDoctypeAdd();
        if(isset($_POST['FormDocTypeAdd'])) {
            $model->attributes = $_POST['FormDocTypeAdd'];
            if($model->validate()) {
                $doctype = Doctype::model()->find('id=:id', array(':id' => $_POST['FormDocTypeAdd']['id']));
                $this->addEditModel($doctype, $model, 'Тип удостоверения личности успешно отредактирован.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionAdd() {
        $model = new FormDoctypeAdd();
        if(isset($_POST['FormDocTypeAdd'])) {
            $model->attributes = $_POST['FormDocTypeAdd'];
            if($model->validate()) {
                $doctype = new Doctype();
                $this->addEditModel($doctype, $model, 'Тип удостоверения личности успешно добавлен.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

}