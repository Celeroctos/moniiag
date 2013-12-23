<?php
class PrivilegesController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        try {
            // Модель формы для добавления и редактирования записи
            $formAddEdit = new FormPrivilegeAdd;

            $this->render('view', array(
                'model' => $formAddEdit
            ));
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionEdit() {
        $model = new FormPrivilegeAdd();
        if(isset($_POST['FormPrivilegeAdd'])) {
            $model->attributes = $_POST['FormPrivilegeAdd'];
            if($model->validate()) {
                $privilege = Privilege::model()->find('id=:id', array(':id' => $_POST['FormPrivilegeAdd']['id']));
                $this->addEditModel($privilege, $model, 'Льгота успешно отредактирована.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionFilter() {
        echo CJSON::encode(array('success' => 'true',
                                 'data' => array()));
    }

    public function actionDelete($id) {
        try {
            $privilege = Privilege::model()->findByPk($id);
            $privilege->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Льгота успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    private function addEditModel($privilege, $model, $msg) {

        $privilege->code = $model->code;
        $privilege->name = $model->name;

        if($privilege->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
        }
    }

    public function actionAdd() {
        $model = new FormPrivilegeAdd();
        if(isset($_POST['FormPrivilegeAdd'])) {
            $model->attributes = $_POST['FormPrivilegeAdd'];
            if($model->validate()) {
                $privilege = new Privilege();
                $this->addEditModel($privilege, $model, 'Льготы успешно добавлена.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }

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

            $model = new Privilege();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $privileges = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(
                array('rows' => $privileges,
                      'total' => $totalPages,
                      'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetone($id) {
        $model = new Privilege();
        $privilege = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $privilege)
        );
    }
}

?>