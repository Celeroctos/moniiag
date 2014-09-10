<?php
class WardsController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        try {
            // Модель формы для добавления и редактирования записи
            $formAddEdit = new FormWardAdd;

            // Список учреждений
            $connection = Yii::app()->db;
            $enterprisesListDb = $connection->createCommand()
                ->select('ep.*')
                ->from('mis.enterprise_params ep')
                ->order('ep.fullname asc')
                ->queryAll();

            $enterprisesList = array();
            foreach($enterprisesListDb as $value) {
                $enterprisesList[(string)$value['id']] = $value['fullname'];
            }

            $this->render('view', array(
                'model' => $formAddEdit,
                'typesList' => $enterprisesList
            ));
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionEdit() {
        $model = new FormWardAdd();
        if(isset($_POST['FormWardAdd'])) {
            $model->attributes = $_POST['FormWardAdd'];
            if($model->validate()) {
                $ward = Ward::model()->find('id=:id', $_POST['FormWardAdd']['id']);

                $this->addEditModel($ward, $model, 'Новое отделение успешно добавлено.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionDelete($id) {
        try {
            $ward = Ward::model()->findByPk($id);
            $ward->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Отделение успешно удалено.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionAdd() {
        $model = new FormWardAdd();
        if(isset($_POST['FormWardAdd'])) {
            $model->attributes = $_POST['FormWardAdd'];
            if($model->validate()) {
                $ward = new Ward();

                $this->addEditModel($ward, $model, 'Новое отделение успешно добавлено.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    private function addEditModel($ward, $model, $msg) {
        $ward->enterprise_id = $model->enterprise;
        $ward->name = $model->name;

        if($ward->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
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

            $model = new Ward();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $wards = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(
                array('rows' => $wards,
                      'total' => $totalPages,
                      'records' => count($num),
                      'success' => true
                )
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetone($id) {
        $model = new Ward();
        $ward = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $ward)
        );
    }

    public function actionGetByEnterprise($id) {
        $model = new Ward();
        $wards = $model->getByEnterprise($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $wards)
        );
    }
}

?>